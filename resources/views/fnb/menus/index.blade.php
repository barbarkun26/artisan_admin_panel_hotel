@extends('layouts.app')

@section('header_title', 'Menu Management')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">F&B Menus</h2>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Manage food and beverage items and their prices.</p>
        </div>
        <a href="{{ route('fnb.menus.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition-colors shadow-sm shadow-indigo-200 dark:shadow-none">
            + Add New Menu
        </a>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-950/50 text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-medium">Name</th>
                        <th class="px-6 py-4 font-medium">Category</th>
                        <th class="px-6 py-4 font-medium">Price</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($menus as $menu)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-900 dark:text-slate-100">{{ $menu->name }}</div>
                                @if($menu->description)
                                    <div class="text-xs text-slate-500 truncate max-w-xs">{{ $menu->description }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                {{ $menu->category->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-slate-700 dark:text-slate-200">
                                Rp {{ number_format($menu->price) }}
                            </td>
                            <td class="px-6 py-4">
                                @if($menu->active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('fnb.menus.edit', $menu) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 font-medium">Edit</a>
                                
                                <form action="{{ route('fnb.menus.destroy', $menu) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this menu item?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 font-medium">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">
                                <div class="flex flex-col items-center">
                                    <svg class="h-12 w-12 text-slate-300 dark:text-slate-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <p>No menu items found. Add some to get started.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($menus->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800">
                {{ $menus->links() }}
            </div>
        @endif
    </div>
@endsection
