<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of all users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
{
    $query = User::query();

    if ($search = $request->input('search')) {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('user_type', 'like', "%{$search}%");
        });
    }

    $users = $query->get();

    if ($request->wantsJson()) {
        return response()->json($users);
    }

    return view('users.index', compact('users', 'search'));
}
    
    // Display user creation form
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate input, making password and gender optional
        $request->validate([
            'email' => 'required|email|unique:users,email', // Ensure email is unique
            'name' => 'required|string|max:255', // Name is required
            'last_name' => 'required|string|max:255', // Last name is required
            'user_type' => 'required|string|max:50', // User type is required
            'gender' => 'nullable|string|max:10', // Gender is optional
            'password' => 'nullable|min:6', // Password is optional, but must be at least 6 characters if provided
        ]);

        // If password is provided, hash it
        $password = $request->password ? Hash::make($request->password) : null;

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => $password, // Hash password if provided
            'user_type' => $request->user_type,
            'gender' => $request->gender, // Gender is optional
        ]);
        
        // Subscribe the user to all lists
        (new SubscriptionController())->subscribeUserToAllLists($user);

        // Return the created user
        return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
    }

    /**
     * Display the specified user by ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
{
    $user = User::findOrFail($id);

    if ($request->wantsJson()) {
        return response()->json($user);
    }

    return view('users.show', compact('user'));
}

    /**
     * Update the specified user by ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
{
    $user = User::findOrFail($id);

    $request->validate([
        'email' => 'email|unique:users,email,' . $id,
        'name' => 'string|max:255',
        'last_name' => 'string|max:255',
        'user_type' => 'string|max:50',
        'gender' => 'nullable|string|max:10',
        'password' => 'nullable|min:6',
    ]);

    $data = $request->only(['name', 'last_name', 'email', 'user_type', 'gender']);

    if ($request->filled('password')) {
        $data['password'] = Hash::make($request->password);
    }

    $user->update($data);

    return redirect()->route('users.show', $user->id)->with('status', 'User updated successfully!');
}

    /**
     * Remove the specified user by ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Find the user by ID
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404); // User not found
        }

        // Delete the user
        $user->delete();

        // Return success message
        return response()->json(['message' => 'User deleted successfully']);
    }
}