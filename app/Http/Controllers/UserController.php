<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
}
