@extends('layouts.app')

@section('header_title', 'Edit User')

@section('content')
<div class="max-w-2xl mx-auto bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">Edit User Account</h2>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Modify account details, roles, or update credentials.</p>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-lg bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400 text-sm">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label for="name" class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">Full Name</label>
                <input type="text" name="name" id="name" required placeholder="User full name" value="{{ old('name', $user->name) }}"
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-indigo-500 text-slate-800 dark:text-slate-100">
            </div>

            <div>
                <label for="email" class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">Email Address</label>
                <input type="email" name="email" id="email" required placeholder="user@artisan.com" value="{{ old('email', $user->email) }}"
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-indigo-500 text-slate-800 dark:text-slate-100">
            </div>

            <div>
                <label for="phone" class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">Phone Number</label>
                <input type="text" name="phone" id="phone" placeholder="e.g. 0812..." value="{{ old('phone', $user->phone) }}"
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-indigo-500 text-slate-800 dark:text-slate-100">
            </div>

            <div>
                <label for="password" class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">Password</label>
                <input type="password" name="password" id="password" placeholder="Leave blank to keep current password"
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-indigo-500 text-slate-800 dark:text-slate-100">
                <span class="text-xs text-slate-400 mt-1 block">Only fill in if you want to change the password.</span>
            </div>

            <div>
                <label for="role" class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">Access Role</label>
                <select name="role" id="role" required
                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-indigo-500 text-slate-800 dark:text-slate-100">
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ old('role', $userRole) === $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">Account Status</label>
                <select name="status" id="status" required
                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-indigo-500 text-slate-800 dark:text-slate-100">
                    <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>

        <div class="flex justify-end space-x-4 border-t border-slate-100 dark:border-slate-800 pt-6">
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-semibold rounded-xl text-sm hover:bg-slate-200 dark:hover:bg-slate-700">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-sm shadow-sm transition-colors">
                Update User Account
            </button>
        </div>
    </form>
</div>
@endsection
