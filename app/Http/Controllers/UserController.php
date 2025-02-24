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
    $sort = $request->input('sort', 'name');         // Default sort column
    $direction = $request->input('direction', 'asc'); // Default sort direction

    $query = User::query();

    // Existing search logic
    if ($search = $request->input('search')) {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('user_type', 'like', "%{$search}%");
        });
    }

    // Apply sorting
    // To avoid SQL injection, ensure $sort is in an allowed list
    $allowedSorts = ['name', 'last_name', 'email', 'user_type', 'gender'];
    if (! in_array($sort, $allowedSorts)) {
        $sort = 'name';
    }
    $direction = ($direction === 'desc') ? 'desc' : 'asc';

    $query->orderBy($sort, $direction);

    $users = $query->get();

    return view('users.index', [
        'users'     => $users,
        'search'    => $search,
        'sort'      => $sort,
        'direction' => $direction,
    ]);
}
    
public function create()
{
    $userTypes = User::whereNotNull('user_type')
                     ->distinct()
                     ->pluck('user_type');

    // Filter out 'master' using a closure
    $userTypes = $userTypes->filter(function ($type) {
        return strtolower($type) !== 'master';
    });

    // Same for gender if needed
    $genders = User::whereNotNull('gender')
                   ->distinct()
                   ->pluck('gender');

    return view('users.create', compact('userTypes', 'genders'));
}

public function store(Request $request)
{
    // Validate input
    $request->validate([
        'email' => 'required|email|unique:users,email',
        'name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'user_type' => 'required|string|max:50',
        'gender' => 'nullable|string|max:10',
        'password' => 'nullable|min:6',
    ]);

    // If password provided, hash it
    $password = $request->filled('password')
        ? Hash::make($request->password)
        : null;

    // Build your data array
    $data = [
        'name'       => $request->name,
        'last_name'  => $request->last_name,
        'email'      => $request->email,
        'password'   => $request->filled('password')
            ? Hash::make($request->password)
            : null,
        // Make sure to store these fields as lowercase:
        'user_type'  => strtolower($request->input('user_type')),
        'gender'     => $request->filled('gender')
                        ? strtolower($request->input('gender'))
                        : null,
    ];

    // Create the user
    $user = User::create($data);


    // Return or redirect
    return redirect()->route('users.index')
                     ->with('status', 'User created successfully!');
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

    $userTypes = User::whereNotNull('user_type')
                     ->distinct()
                     ->pluck('user_type');
    // filter out master
    $userTypes = $userTypes->filter(fn($type) => strtolower($type) !== 'master');

    $genders = User::whereNotNull('gender')
                   ->distinct()
                   ->pluck('gender');

    if ($request->wantsJson()) {
        return response()->json($user);
    }

    return view('users.show', compact('user', 'userTypes', 'genders'));
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
        'name' => 'nullable|string|max:255',
        'last_name' => 'nullable|string|max:255',
        'user_type' => 'string|max:50',
        'gender' => 'nullable|string|max:10',
        'password' => 'nullable|min:6',
    ]);

    // Find user
    $user = User::findOrFail($id);

    // Build your updated data
    $data = $request->only(['name', 'last_name', 'email']);
    // If password is provided
    if ($request->filled('password')) {
        $data['password'] = Hash::make($request->password);
    }

    // Always store these fields in lowercase
    $data['user_type'] = strtolower($request->input('user_type'));
    $data['gender']    = $request->filled('gender')
        ? strtolower($request->input('gender'))
        : null;

    $user->update($data);

    return redirect()->route('users.show', $user->id)
                     ->with('status', 'User updated successfully!');
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