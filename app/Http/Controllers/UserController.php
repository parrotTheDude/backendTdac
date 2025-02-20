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
    public function index()
    {
        $users = User::all(); // Get all users
        return response()->json($users); // Return users as JSON
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
    public function show($id)
    {
        // Find user by ID
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404); // User not found
        }

        // Return the user
        return response()->json($user);
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
        // Find the user by ID
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404); // User not found
        }

        // Validate input
        $request->validate([
            'email' => 'email|unique:users,email,' . $id, // Ensure email is unique except for this user
            'name' => 'string|max:255', // Optional: Name is a string with max length
            'last_name' => 'string|max:255', // Optional: Last name string
            'user_type' => 'string|max:50', // Optional: User type string
            'gender' => 'nullable|string|max:10', // Gender is optional
            'password' => 'nullable|min:6', // Password is optional
        ]);

        // If password is provided, hash it
        $password = $request->password ? Hash::make($request->password) : $user->password;

        // Update the user
        $user->update($request->only(['name', 'last_name', 'email', 'user_type', 'gender']) + ['password' => $password]);


        // Return the updated user
        return response()->json(['message' => 'User updated successfully', 'user' => $user]);
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