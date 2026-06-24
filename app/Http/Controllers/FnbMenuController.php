<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\FnbCategory;
use App\Models\FnbMenu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FnbMenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $menus = FnbMenu::with('category')->orderBy('id', 'desc')->paginate(15);
        return view('fnb.menus.index', compact('menus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categories = FnbCategory::all();
        return view('fnb.menus.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'category_id' => 'required|exists:fnb_categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'active' => 'boolean',
        ]);

        $menu = FnbMenu::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'active' => $request->has('active') ? true : false,
        ]);

        ActivityLog::log(
            Auth::id(),
            'Food & Beverage',
            'Menu Added',
            "Added new menu item: {$menu->name} (Rp " . number_format($menu->price) . ")"
        );

        return redirect()->route('fnb.menus.index')->with('success', 'Menu item added successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FnbMenu $menu): View
    {
        $categories = FnbCategory::all();
        return view('fnb.menus.edit', compact('menu', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FnbMenu $menu): RedirectResponse
    {
        $request->validate([
            'category_id' => 'required|exists:fnb_categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'active' => 'boolean',
        ]);

        $menu->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'active' => $request->has('active') ? true : false,
        ]);

        ActivityLog::log(
            Auth::id(),
            'Food & Beverage',
            'Menu Updated',
            "Updated menu item: {$menu->name}"
        );

        return redirect()->route('fnb.menus.index')->with('success', 'Menu item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FnbMenu $menu): RedirectResponse
    {
        $name = $menu->name;
        
        // Cannot delete if there are orders using this menu
        if ($menu->orderDetails()->exists()) {
            return back()->with('error', 'Cannot delete this menu item because it is used in orders. Consider making it inactive instead.');
        }

        $menu->delete();

        ActivityLog::log(
            Auth::id(),
            'Food & Beverage',
            'Menu Deleted',
            "Deleted menu item: {$name}"
        );

        return redirect()->route('fnb.menus.index')->with('success', 'Menu item deleted successfully.');
    }
}
