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

            return redirect()->back()->with('status', 'Subscribed successfully!');
        } catch (\Exception $e) {
            // Catch the exception and return the error message
            return redirect()->back()->with('status', 'Error subscribing');
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

        // Instead of returning JSON:
        return redirect()->back()->with('status', 'Unsubscribed successfully!');
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
    
public function listIndex(Request $request)
{
    // If you still want to handle JSON
    if ($request->wantsJson()) {
        $lists = Subscription::select('list_name')->distinct()->get();
        return response()->json(['lists' => $lists]);
    }

    // In a single query, count how many are subscribed vs. unsubscribed per list
    // Uses raw SUM trick: (subscribed = 1) => 1 for subscribed, 0 for unsubscribed
    // and vice versa
    $lists = \DB::table('subscriptions')
        ->select('list_name',
            \DB::raw("SUM(CASE WHEN subscribed = 1 THEN 1 ELSE 0 END) as subscribed_count"),
            \DB::raw("SUM(CASE WHEN subscribed = 0 THEN 1 ELSE 0 END) as unsubscribed_count")
        )
        ->groupBy('list_name')
        ->get();

    return view('subscriptions.index', compact('lists'));
}

public function listShow(Request $request, $listName)
{
    // If user wants JSON, fine:
    if ($request->wantsJson()) {
        $subscribers = Subscription::with('user')
            ->where('list_name', $listName)
            ->where('subscribed', true)
            ->get();
        return response()->json(['list_name' => $listName, 'subscribers' => $subscribers]);
    }

    // Otherwise return the Blade view
    $subscribers = Subscription::with('user')
        ->where('list_name', $listName)
        ->where('subscribed', true)
        ->get();

    return view('subscriptions.show', [
        'listName' => $listName,
        'subscribers' => $subscribers,
    ]);
}


}