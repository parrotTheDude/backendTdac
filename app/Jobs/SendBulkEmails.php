<?php

namespace App\Jobs;

use App\Models\Subscription;
use App\Models\BulkEmail;
use Postmark\PostmarkClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBulkEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    protected $bulkEmail;
    protected $templateId;
    protected $variables;
    protected $recipientList;

    public function __construct(BulkEmail $bulkEmail, int $templateId, array $variables, string $recipientList)
    {
        \Log::info('Job constructor started');
        $this->bulkEmail = $bulkEmail;
        $this->templateId = $templateId;
        $this->variables = $variables;
        $this->recipientList = $recipientList;
    }

    public function handle()
    {
        $client = new PostmarkClient(config('services.postmark.token'));
        $fromEmail = config('services.postmark.from_email');

        \Log::info('DEBUG: Bulk email job about to send', [
            'list_name' => $this->recipientList,
            'templateId' => $this->templateId,
            'variables' => $this->variables,
        ]);

        // Fetch user emails
        $emails = Subscription::where('list_name', $this->recipientList)
            ->where('subscribed', true)
            ->with('user')
            ->get()
            ->pluck('user.email')
            ->unique()
            ->values();

        \Log::info('DEBUG: Emails fetched', [
            'count' => $emails->count(),
            'emails' => $emails,
        ]);

        if ($emails->isEmpty()) {
            \Log::warning('No recipients found for list', ['list_name' => $this->recipientList]);
            return; // stop if no recipients
        }

        $batchSize = 500;
        $totalSent = 0;

        foreach ($emails->chunk($batchSize) as $emailBatch) {
            \Log::info('Attempting Postmark sends', [
                'batchSize' => count($emailBatch),
                'list_name' => $this->recipientList
            ]);

            foreach ($emailBatch as $email) {
                try {
                    // Older method: sendEmailWithTemplate()
                    $client->sendEmailWithTemplate(
                        $fromEmail,        // 'From'
                        $email,            // 'To'
                        $this->templateId, // 'TemplateId'
                        $this->variables,  // 'TemplateModel'
                        true,              // inline css
                        $this->bulkEmail->template_name, // Tag
                        true,              // track opens
                        null,              // replyTo
                        null,              // cc
                        null,              // bcc
                        null,              // headers
                        null,              // attachments
                        'None',            // trackLinks
                        null,              // metaData
                        $this->recipientList // messageStream
                    );

                    $totalSent++;
                    $this->bulkEmail->update(['emails_sent' => $totalSent]);
                } catch (\Exception $e) {
                    \Log::error("Bulk Email sending failed (single): {$e->getMessage()}", [
                        'to' => $email
                    ]);
                }
            }
        }

        \Log::info('Bulk email job completed', ['totalSent' => $totalSent]);
    }
}