<?php
namespace App\Http\Controllers;

use Postmark\PostmarkClient;
use Illuminate\Http\Request;
use App\Models\User;

class CalendarReleaseController extends Controller
{
    public function sendBatchCalendarRelease(Request $request)
    {
        // Get templateId and tag from the query string
        $templateId = $request->query('templateId');
        $tag = $request->query('tag');

        // Ensure both parameters are provided
        if (!$templateId || !$tag) {
            return response()->json(['error' => 'TemplateId and tag are required'], 400);
        }
        try {
            $client = new PostmarkClient(env('POSTMARK_TOKEN'));

            // Get all emails of users subscribed to the 'testBatch' list
            $emails = User::whereHas('subscriptions', function ($query) {
                $query->where('list_name', 'testBatch')->where('subscribed', true);
            })->pluck('email');

            if ($emails->isEmpty()) {
                return response()->json(['message' => 'No users subscribed to the newsletter'], 200);
            }

            // Send emails to each user
            foreach ($emails as $email) {
                try {
                    \Log::info('Attempting to send email to ' . $email);
                    $response = $client->sendEmailWithTemplate(
                        env('FROM_EMAIL'),
                        $email,
                        $templateId,
                        ['email' => $email, 'product_url' => 'https://thatdisabilityadventurecompany.com.au/'],
                        true, 
                        $tag, 
                        true, 
                        NULL, 
                        NULL, 
                        NULL, 
                        NULL,
                        NULL,
                        'none', 
                        NULL, 
                        'calender-release'
                    );
                    
                    // Log Postmark response (success or failure)
                    \Log::info('Postmark response for email ' . $email . ': ' . json_encode($response));
                } catch (\Exception $e) {
                    \Log::error('Failed to send email to ' . $email . ': ' . $e->getMessage());
                    continue; // Continue to the next email if one fails
                }
            }

            return response()->json(['message' => 'Batch email sent successfully']);
        } catch (\Exception $e) {
            \Log::error('Error with Postmark API: ' . $e->getMessage());
            return response()->json(['error' => 'Error with Postmark API', 'message' => $e->getMessage()], 500);
        }
    }
}