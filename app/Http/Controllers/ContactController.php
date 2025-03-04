<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactSubmission;
use App\Models\BlockedContact;
use Illuminate\Support\Facades\Log;
use Postmark\PostmarkClient;

class ContactController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = new PostmarkClient(config('services.postmark.token'));
    }

    public function store(Request $request)
    {
        // Honeypot check
        if (!empty($request->honeypot) || !empty($request->user_comment)) {
            Log::warning("Bot detected. Ignoring submission.");
            return response()->json(["success" => false, "message" => "Spam detected."], 403);
        }

        // Validate and sanitize input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|regex:/^0[0-9]{9}$/',
            'age' => 'required|in:18-22,23-30,31-39,40-45',
            'location' => 'required|in:Melbourne,Gippsland,Mornington Peninsula',
            'preferred_contact' => 'required|in:email,phone',
            'message' => 'required|string',
        ]);

        // Check if email is blocked
        if (BlockedContact::where('email', $validated['email'])->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This email has been blocked.'
            ], 403);
        }

        // Store the submission
        $submission = ContactSubmission::create($validated);

        // Send confirmation email to the user
        try {
            $this->client->sendEmailWithTemplate(
                config('services.postmark.from_email'),
                "hello@jbowerman.com", //$validated['email']
                38711879,
                ['name' => $validated['name']],
                true,
                "contact-form-receipt",
                true
            );
        } catch (\Exception $e) {
            Log::error("User confirmation email failed: " . $e->getMessage(), ['email' => $validated['email']]);
        }

        // Send inquiry email to the company
        try {
            $this->client->sendEmailWithTemplate(
                config('services.postmark.from_email'),
                "hello@jbowerman.com",
                38713021,
                [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'telephone' => $validated['phone'],
                    'message' => $validated['message'],
                    'age_range' => $validated['age'],
                    'location' => $validated['location'],
                    'preferred_contact' => ucfirst($validated['preferred_contact'])
                ],
                true,
                "contact-form-enquiry",
                false
            );
        } catch (\Exception $e) {
            Log::error("Company notification email failed: " . $e->getMessage(), ['email' => $validated['email']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Your message has been received. We will get back to you soon!'
        ]);
    }

    public function index()
    {
        $submissions = ContactSubmission::orderBy('created_at', 'desc')->get();
        return view('admin.contact-submissions', compact('submissions'));
    }

    public function markSpam($id)
    {
        $submission = ContactSubmission::findOrFail($id);
        $submission->update(['is_spam' => true]);

        // Add to blocked list
        BlockedContact::updateOrCreate(['email' => $submission->email]);

        return redirect()->route('contact.index')->with('status', 'Marked as spam.');
    }

    public function unblockEmail($id)
    {
        $blockedEmail = BlockedContact::findOrFail($id);
        $blockedEmail->delete();

        return redirect()->route('contact.index')->with('status', 'Email unblocked.');
    }
}