<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Subscription;

class NewListController extends Controller
{
    public function create()
{
    // Fetch users for custom emails
    $users = User::orderBy('email')->get();

    // Fetch existing lists
    $existingLists = Subscription::select('list_name')->distinct()->get();

    return view('subscriptions.create-list', compact('users', 'existingLists'));
}

    public function store(Request $request)
{
    $request->validate([
        'friendly_name' => 'required|string|max:255',
        // 'lists' => 'array', // optional if user picks no existing lists
        // 'emails' => 'nullable|string', // a textarea for custom emails
    ]);

    // 1) Generate code-like list_name
    $friendlyName = trim($request->friendly_name);
    $listName = strtolower(str_replace(' ', '_', $friendlyName));

    // 2) Collect user IDs from existing lists
    $selectedLists = $request->input('lists', []);  // array of list_name strings
    $userIdsFromLists = [];

    if (!empty($selectedLists)) {
        // Query the subscriptions table for all 'subscribed' users in those lists
        $userIdsFromLists = \App\Models\Subscription::whereIn('list_name', $selectedLists)
            ->where('subscribed', true)
            ->pluck('user_id')
            ->unique()
            ->toArray();
    }

    // 3) Collect user IDs from typed emails
    $typedEmails = $request->input('emails'); // e.g. one-per-line
    $userIdsFromEmails = [];

    if ($typedEmails) {
        $emailLines = array_filter(array_map('trim', explode("\n", $typedEmails)));

        foreach ($emailLines as $emailLine) {
            if (!filter_var($emailLine, FILTER_VALIDATE_EMAIL)) {
                // Optionally skip or throw an error
                continue;
            }

            // Find or create user by email
            $user = \App\Models\User::firstOrCreate(['email' => $emailLine]);
            $userIdsFromEmails[] = $user->id;
        }
    }

    // 4) Merge all user IDs
    $allUserIds = array_unique(array_merge($userIdsFromLists, $userIdsFromEmails));

    // 5) Subscribe them to the new list
    foreach ($allUserIds as $uid) {
        \App\Models\Subscription::updateOrCreate(
            [
                'user_id'   => $uid,
                'list_name' => $listName,
            ],
            [
                'subscribed' => true,
            ]
        );
    }

    // 6) Redirect
    return redirect()->route('subscriptions.index')
                     ->with('status', "New list '$friendlyName' created successfully!");
}
}