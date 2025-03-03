<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handleSubscriptionChange(Request $request)
    {
        // Log incoming webhook data
        Log::info('Received Subscription Webhook', $request->all());

        // Ensure request contains necessary data
        $request->validate([
            'Recipient' => 'required|email',
            'MessageStream' => 'required|string',
            'SuppressSending' => 'required|boolean',
        ]);

        $email = $request->input('Recipient');
        $messageStream = strtolower($request->input('MessageStream'));
        $isUnsubscribed = $request->input('SuppressSending');

        // Only process for newsletter or internal-events streams
        if (!in_array($messageStream, ['newsletter', 'bonus-event'])) {
            Log::info("Ignoring subscription change for stream: $messageStream");
            return response()->json(['message' => 'Ignored'], 200);
        }

        // Find user by email
        $user = User::where('email', $email)->first();

        if (!$user) {
            Log::warning("No user found with email: $email");
            return response()->json(['message' => 'User not found'], 404);
        }

        // Find or create the subscription for the user
        $subscription = Subscription::updateOrCreate(
            ['user_id' => $user->id, 'list_name' => $messageStream],
            ['subscribed' => !$isUnsubscribed]
        );

        if ($isUnsubscribed) {
            Log::info("User $email unsubscribed from $messageStream");
        } else {
            Log::info("User $email subscribed to $messageStream");
        }

        return response()->json(['message' => 'Subscription change processed'], 200);
    }
}