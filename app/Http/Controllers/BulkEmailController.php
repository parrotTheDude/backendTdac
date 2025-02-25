<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Postmark\PostmarkClient;
use App\Models\Subscription;
use App\Models\BulkEmail;
use App\Jobs\SendBulkEmails;

class BulkEmailController extends Controller
{
    protected $client;

    public function __construct()
    {
        // older Postmark library
        $this->client = new PostmarkClient(config('services.postmark.token'));
    }

    public function index()
    {
        // 1) Fetch Postmark templates
        $templates = $this->client->listTemplates()->Templates;

        // 2) Parse variables from each template
        $templatesWithDetails = collect($templates)->map(function ($template) {
            $details = $this->client->getTemplate($template->getTemplateId());

            // gather all {{ variable }} references from subject, HTML, text
            preg_match_all(
                '/{{\\s*([^}]+)\\s*}}/',
                $details->getHtmlBody() . $details->getTextBody() . $details->getSubject(),
                $matches
            );

            return [
                'id'       => $template->getTemplateId(),
                'name'     => $template->getName(),
                'variables'=> array_unique($matches[1]),
            ];
        })->toArray();

        // distinct subscription lists
        $lists = Subscription::select('list_name')->distinct()->get();
        // show entire log or partial
        $bulkEmails = BulkEmail::latest()->get();

        return view('bulk-emails.index', compact('templatesWithDetails', 'lists', 'bulkEmails'));
    }

    public function send(Request $request)
    {
        // Validate all fields, including optional testEmail
        $validated = $request->validate([
            'template_id'    => 'required|numeric',
            'template_name'  => 'required|string',
            'recipient_list' => 'required|string',
            'variables'      => 'nullable|array',
            'testEmail'      => 'nullable|email',
        ]);

        // Variables default to empty array if not provided
        $variables = $validated['variables'] ?? [];

        // Check if we have a single testEmail
        if ($request->filled('testEmail')) {
            // 1) Create a BulkEmail record for this single test
            $bulkEmail = BulkEmail::create([
                'template_id'    => $validated['template_id'],
                'template_name'  => $validated['template_name'],
                'variables'      => json_encode($variables),
                'recipient_lists'=> json_encode([$request->testEmail]),
                'emails_sent'    => 0,
            ]);

            // 2) Immediately send a single email using older Postmark library
            //    via sendEmailWithTemplate()
            try {
                $this->client->sendEmailWithTemplate(
                    config('services.postmark.from_email'),
                    $request->testEmail,
                    (int) $validated['template_id'],
                    $variables
                );

                // Mark that 1 email was sent
                $bulkEmail->update(['emails_sent' => 1]);

                \Log::info('Test email sent successfully', [
                    'testEmail' => $request->testEmail,
                    'templateId'=> $validated['template_id'],
                ]);
            } catch (\Exception $e) {
                \Log::error('Test email failed: ' . $e->getMessage(), [
                    'testEmail' => $request->testEmail,
                ]);
            }

            // Return JSON or you can redirect
            return response()->json([
                'bulk_email_id' => $bulkEmail->id,
                'message'       => 'Test email sent!',
            ]);
        }

        // 1. Calculate the real total recipients
        $totalCount = Subscription::where('list_name',                     $validated['recipient_list'])
            ->where('subscribed', true)
            ->count();

        // 2. Create BulkEmail
        $bulkEmail = BulkEmail::create([
            'template_id'     => $validated['template_id'],
            'template_name'   => $validated['template_name'],
            'variables'       => json_encode($validated['variables'] ?? []),
            'recipient_lists' => json_encode([$validated['recipient_list']]),
            'emails_sent'     => 0,
        ]);

        // 3. Dispatch the job
        SendBulkEmails::dispatch(
            $bulkEmail,
            (int) $validated['template_id'],
            $validated['variables'] ?? [],
            $validated['recipient_list']
        );

        // 4. Return the real total in JSON
        return response()->json([
            'bulk_email_id' => $bulkEmail->id,
            'total'         => $totalCount, // the real number of recipients
        ]);
    }

    public function progress($id)
    {
        $bulkEmail = BulkEmail::findOrFail($id);
        return response()->json([
            'emails_sent' => $bulkEmail->emails_sent
        ]);
    }

    public function history()
    {
        $bulkEmails = BulkEmail::latest()->get();
        return view('bulk-emails.history', compact('bulkEmails'));
    }

    public function subscriberCount($listName)
    {
        $count = Subscription::where('list_name', $listName)
                    ->where('subscribed', true)
                    ->count();

        return response()->json(['count' => $count]);
    }
}