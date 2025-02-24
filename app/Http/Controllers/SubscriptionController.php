<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Subscription;

class SubscriptionController extends Controller
{
    // Fetch all subscriptions with the user details
    public function index()
    {
        return response()->json(Subscription::with('user')->get());
    }

    // Subscribe a user to a list
    public function subscribe(Request $request)
    {
        try {
            // Validate the input
            $request->validate([
                'email' => 'required|email',
                'list_name' => 'required|string'
            ]);

            // Try to find or create the user by email
            $user = User::firstOrCreate(['email' => $request->email]);

            // Check if the user is already subscribed to this list
            $existingSubscription = Subscription::where('user_id', $user->id)
                ->where('list_name', $request->list_name)
                ->first();

            if ($existingSubscription && $existingSubscription->subscribed) {
                return response()->json(['message' => 'User is already subscribed to this list'], 400);
            }

            // Create or update the subscription record
            Subscription::updateOrCreate(
                ['user_id' => $user->id, 'list_name' => $request->list_name],
                ['subscribed' => true] // Set subscribed to true
            );

            return response()->json(['message' => 'Subscribed successfully']);
        } catch (\Exception $e) {
            // Catch the exception and return the error message
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Unsubscribe a user from a mailing list
    public function unsubscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'list_name' => 'required|string'
        ]);

        // Find user by email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Find the subscription by user_id and list_name
        $subscription = Subscription::where('user_id', $user->id)
            ->where('list_name', $request->list_name)
            ->first();

        if (!$subscription) {
            return response()->json(['message' => 'Subscription not found'], 404);
        }

        // Unsubscribe the user by setting 'subscribed' to false
        $subscription->update(['subscribed' => false]);

        return response()->json(['message' => 'Unsubscribed successfully']);
    }
    
    public function subscribeUserToAllLists(User $user)
    {
        // Define the list types (email types)
        $listTypes = ['newsletter', 'calendar_release'];  // Add other list types if needed

        // Subscribe the user to all the list types
        foreach ($listTypes as $listType) {
            Subscription::create([
                'user_id' => $user->id,
                'list_name' => $listType,
                'subscribed' => true, // Automatically subscribe the user
            ]);
        }
    }
}