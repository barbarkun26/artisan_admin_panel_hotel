<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(): View
    {
        // Get all users with their roles
        $users = User::with('roles')->get();
        
        // Get active sessions in the last 15 minutes
        $activeSessions = DB::table('sessions')
            ->whereNotNull('user_id')
            ->where('last_activity', '>=', Carbon::now()->subMinutes(15)->getTimestamp())
            ->pluck('last_activity', 'user_id');

        // Map users to append online status
        $users->map(function ($user) use ($activeSessions) {
            $user->is_online = $activeSessions->has($user->id);
            if ($user->is_online) {
                $user->last_seen = Carbon::createFromTimestamp($activeSessions->get($user->id))->diffForHumans();
            } else {
                $user->last_seen = 'Offline';
            }
            return $user;
        });

        // Get count per role
        $roleCounts = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('roles.name', DB::raw('count(*) as total'))
            ->where('model_has_roles.model_type', User::class)
            ->groupBy('roles.name')
            ->get();

        return view('admin.users.index', compact('users', 'roleCounts'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        $roles = \Spatie\Permission\Models\Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone' => $request->phone,
            'status' => $request->status,
        ]);

        $user->assignRole($request->role);

        ActivityLog::log(
            Auth::id(),
            'User Management',
            'Create User',
            "Created user: {$user->name} with role {$request->role}"
        );

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        $roles = \Spatie\Permission\Models\Role::all();
        $userRole = $user->roles->first()?->name;
        return view('admin.users.edit', compact('user', 'roles', 'userRole'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
            'role' => 'required|exists:roles,name',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        // Sync roles
        $user->syncRoles([$request->role]);

        ActivityLog::log(
            Auth::id(),
            'User Management',
            'Update User',
            "Updated user: {$user->name} (Role: {$request->role})"
        );

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Don't allow self-deletion
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account.');
        }

        $name = $user->name;
        $user->delete();

        ActivityLog::log(
            Auth::id(),
            'User Management',
            'Delete User',
            "Deleted user: {$name}"
        );

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
