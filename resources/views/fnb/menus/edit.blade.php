@extends('layouts.app')

@section('header_title', 'Edit Menu Item')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('fnb.menus.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Menus
            </a>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
            <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100 mb-6">Edit Food/Beverage: {{ $menu->name }}</h2>

            <form action="{{ route('fnb.menus.update', $menu) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-5">
                    <!-- Category -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Category</label>
                        <select name="category_id" id="category_id" required class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $menu->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Item Name</label>
                        <input type="text" name="name" id="name" required value="{{ old('name', $menu->name) }}" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Price -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Price (Rp)</label>
                        <input type="number" name="price" id="price" required min="0" step="1000" value="{{ old('price', round($menu->price)) }}" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('price')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Description (Optional)</label>
                        <textarea name="description" id="description" rows="3" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $menu->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Active Status -->
                    <div class="flex items-center">
                        <input type="checkbox" name="active" id="active" value="1" {{ old('active', $menu->active) ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 h-4 w-4">
                        <label for="active" class="ml-2 block text-sm text-slate-700 dark:text-slate-300">
                            Available / Active
                        </label>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition-colors shadow-sm">
                        Update Menu Item
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
