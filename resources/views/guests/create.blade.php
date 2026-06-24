@extends('layouts.app')

@section('header_title', 'Register New Guest')

@section('content')
<div class="max-w-2xl mx-auto bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
    <form action="{{ route('guests.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label for="name" class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">Full Name</label>
                <input type="text" name="name" id="name" required placeholder="Guest full name"
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label for="identity_type" class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">Identity Type</label>
                <select name="identity_type" id="identity_type" required
                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-indigo-500">
                    <option value="KTP">KTP</option>
                    <option value="Passport">Passport</option>
                    <option value="SIM">SIM (Driving License)</option>
                    <option value="Other">Other ID</option>
                </select>
            </div>

            <div>
                <label for="identity_number" class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">Identity Number</label>
                <input type="text" name="identity_number" id="identity_number" required placeholder="e.g. 3201234..."
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label for="phone" class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">Phone Number</label>
                <input type="text" name="phone" id="phone" required placeholder="e.g. 0812..."
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label for="email" class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">Email Address</label>
                <input type="email" name="email" id="email" placeholder="Optional"
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label for="profession" class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">Profession</label>
                <input type="text" name="profession" id="profession" placeholder="e.g. Employee"
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label for="company" class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">Company</label>
                <input type="text" name="company" id="company" placeholder="Company Name"
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label for="nationality" class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">Nationality</label>
                <input type="text" name="nationality" id="nationality" placeholder="e.g. Indonesian"
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label for="birth_date" class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">Birth Date</label>
                <input type="date" name="birth_date" id="birth_date"
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-indigo-500">
            </div>

            <div class="md:col-span-2">
                <label for="member_card_no" class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">Member Card No.</label>
                <input type="text" name="member_card_no" id="member_card_no" placeholder="Optional"
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-indigo-500">
            </div>

            <div class="md:col-span-2">
                <label for="address" class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">Home Address</label>
                <textarea name="address" id="address" rows="3" placeholder="Street, city, country..."
                          class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-indigo-500"></textarea>
            </div>
        </div>

        <div class="flex justify-end space-x-4 border-t border-slate-100 dark:border-slate-800 pt-6">
            <a href="{{ route('guests.index') }}" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-semibold rounded-xl text-sm hover:bg-slate-200 dark:hover:bg-slate-700">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-slate-950 font-bold rounded-xl text-sm shadow-sm">
                Save Guest Profile
            </button>
        </div>
    </form>
</div>
@endsection
