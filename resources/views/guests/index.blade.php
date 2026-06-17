@extends('layouts.app')

@section('header_title', 'Guests Directory')

@section('content')
<div class="space-y-6">
    <!-- Search and Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form action="{{ route('guests.index') }}" method="GET" class="flex-1 flex gap-4">
            <input type="text" name="search" placeholder="Search by name, guest code, phone..." value="{{ request('search') }}"
                   class="w-full max-w-md px-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:outline-none focus:border-amber-500">
            <button type="submit" class="px-4 py-2 bg-slate-900 dark:bg-slate-700 text-white rounded-xl text-sm font-semibold hover:bg-slate-800">
                Search
            </button>
        </form>
        
        <a href="{{ route('guests.create') }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl text-sm shadow-sm">
            + Register Guest
        </a>
    </div>

    <!-- Guests Table -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 text-xs uppercase text-slate-400">
                        <th class="py-3 font-semibold">Guest Code</th>
                        <th class="py-3 font-semibold">Name</th>
                        <th class="py-3 font-semibold">Identity Number</th>
                        <th class="py-3 font-semibold">Phone</th>
                        <th class="py-3 font-semibold">Email</th>
                        <th class="py-3 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 text-sm">
                    @forelse($guests as $guest)
                        <tr>
                            <td class="py-4 font-mono font-medium text-slate-900 dark:text-white">{{ $guest->guest_code }}</td>
                            <td class="py-4 font-medium">{{ $guest->name }}</td>
                            <td class="py-4 text-xs">
                                <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-500 mr-1">{{ $guest->identity_type }}</span>
                                <span>{{ $guest->identity_number }}</span>
                            </td>
                            <td class="py-4">{{ $guest->phone }}</td>
                            <td class="py-4 text-xs">{{ $guest->email ?? 'N/A' }}</td>
                            <td class="py-4 text-right">
                                <a href="{{ route('guests.edit', $guest->id) }}" class="text-amber-500 hover:text-amber-600 font-medium">
                                    Edit Profile
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">No guests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $guests->links() }}
        </div>
    </div>
</div>
@endsection
