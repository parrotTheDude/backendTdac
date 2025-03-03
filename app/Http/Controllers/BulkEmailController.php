<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Postmark\PostmarkClient;
use App\Models\Subscription;
use App\Models\BulkEmail;
use App\Models\User;

class BulkEmailController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = new PostmarkClient(config('services.postmark.token'));
    }

    public function index()
    {
        $templates = $this->client->listTemplates()->Templates;

        $filteredTemplates = collect($templates)->filter(function ($template) {
            return str_contains(strtolower($template->getName()), 'calendar release') ||
                   str_contains(strtolower($template->getName()), 'newsletter') ||
                   str_contains(strtolower($template->getName()), 'teens') ||
                   str_contains(strtolower($template->getName()), 'bonus');
        });

        $templatesWithDetails = $filteredTemplates->map(function ($template) {
            $details = $this->client->getTemplate($template->getTemplateId());

            preg_match_all(
                '/{{\s*([^}]+)\s*}}/',
                $details->getHtmlBody() . $details->getTextBody() . $details->getSubject(),
                $matches
            );

            return [
                'id'       => $template->getTemplateId(),
                'name'     => $template->getName(),
                'variables'=> array_unique($matches[1]),
            ];
        })->toArray();

        $lists = Subscription::select('list_name')->distinct()->get();
        $bulkEmails = BulkEmail::latest()->get();

        return view('bulk-emails.index', compact('templatesWithDetails', 'lists', 'bulkEmails'));
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'template_id'    => 'required|numeric',
            'template_name'  => 'required|string',
            'recipient_list' => 'required|string',
            'variables'      => 'nullable|array',
            'testEmail'      => 'nullable|email',
        ]);

        $variables = $validated['variables'] ?? [];
        $recipientList = $validated['recipient_list'];
        $messageStream = ($recipientList === 'newsletter') ? 'newsletter' : 'bonus-event';
        $fromEmail = ($recipientList === 'newsletter') ? 'newsletter@tdacvic.com' : 'events@tdacvic.com';

        if ($request->filled('testEmail')) {
            return $this->sendTestEmail($validated, $request->testEmail, $messageStream);
        }

        $emails = Subscription::where('list_name', $recipientList)
            ->where('subscribed', true)
            ->with('user')
            ->get()
            ->pluck('user.email')
            ->unique()
            ->values();

        if ($emails->isEmpty()) {
            \Log::warning('No recipients found for list', ['list_name' => $recipientList]);
            return response()->json(['error' => 'No recipients found for this list.'], 400);
        }

        $totalCount = $emails->count();

        $bulkEmail = BulkEmail::create([
            'template_id'     => $validated['template_id'],
            'template_name'   => $validated['template_name'],
            'variables'       => json_encode($variables),
            'recipient_lists' => json_encode([$recipientList]),
            'emails_sent'     => 0,
        ]);

        $batchSize = 500;
        $totalSent = 0;

        foreach ($emails->chunk($batchSize) as $emailBatch) {
            foreach ($emailBatch as $email) {
                try {
                    $this->client->sendEmailWithTemplate(
                        $fromEmail,
                        $email,
                        (int) $validated['template_id'],
                        $variables,
                        true,
                        $validated['template_name'],
                        true,
                        null,
                        null,
                        null,
                        null,
                        null,
                        'None',
                        null,
                        $messageStream
                    );

                    $totalSent++;
                    $bulkEmail->update(['emails_sent' => $totalSent]);
                } catch (\Exception $e) {
                    \Log::error("Bulk Email sending failed (single): {$e->getMessage()}", [
                        'to' => $email
                    ]);
                }
            }
        }

        \Log::info('Bulk email sending completed', ['totalSent' => $totalSent]);

        return response()->json([
            'bulk_email_id' => $bulkEmail->id,
            'total'         => $totalCount,
            'sent'          => $totalSent,
        ]);
    }

    private function sendTestEmail($validated, $testEmail, $messageStream)
    {
        try {
            $bulkEmail = BulkEmail::create([
                'template_id'    => $validated['template_id'],
                'template_name'  => $validated['template_name'],
                'variables'      => json_encode($validated['variables'] ?? []),
                'recipient_lists'=> json_encode([$testEmail]),
                'emails_sent'    => 0,
            ]);

            $this->client->sendEmailWithTemplate(
                config('services.postmark.from_email'),
                $testEmail,
                (int) $validated['template_id'],
                $validated['variables'] ?? [],
                true,
                $validated['template_name'],
                true,
                null,
                null,
                null,
                null,
                null,
                'None',
                null,
                $messageStream
            );

            $bulkEmail->update(['emails_sent' => 1]);

            return response()->json([
                'bulk_email_id' => $bulkEmail->id,
                'message'       => 'Test email sent!',
            ]);
        } catch (\Exception $e) {
            \Log::error('Test email failed: ' . $e->getMessage(), [
                'testEmail' => $testEmail,
            ]);
            return response()->json([
                'error' => 'Failed to send test email.'
            ], 500);
        }
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