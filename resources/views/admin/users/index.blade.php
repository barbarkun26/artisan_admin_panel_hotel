@extends('layouts.app')

@section('header_title', 'User Management')

@section('content')
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($roleCounts as $roleCount)
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-4 flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">{{ $roleCount->name }}</p>
                    <p class="text-2xl font-bold mt-1 text-slate-800 dark:text-slate-100">{{ $roleCount->total }}</p>
                </div>
                <div class="h-10 w-10 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
        @endforeach
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="p-5 border-b border-slate-200 dark:border-slate-800">
            <h2 class="text-lg font-semibold">All Users</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-950/50 text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-medium">Name</th>
                        <th class="px-6 py-4 font-medium">Role</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium">Last Seen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @foreach($users as $user)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-900 dark:text-slate-100">{{ $user->name }}</div>
                                <div class="text-sm text-slate-500">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @foreach($user->roles as $role)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300">
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="px-6 py-4">
                                @if($user->is_online)
                                    <span class="inline-flex items-center space-x-1 text-emerald-600 dark:text-emerald-400 text-sm font-medium">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span>Online</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center space-x-1 text-slate-400 text-sm font-medium">
                                        <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                        <span>Offline</span>
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500">
                                {{ $user->last_seen }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
