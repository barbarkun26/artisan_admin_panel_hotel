<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuestController extends Controller
{
    public function index(Request $request): View
    {
        $query = Guest::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('guest_code', 'like', "%{$search}%");
        }

        $guests = $query->orderBy('name')->paginate(15);

        return view('guests.index', compact('guests'));
    }

    public function create(): View
    {
        return view('guests.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'identity_type' => 'required|string',
            'identity_number' => 'required|string|max:50',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'profession' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'member_card_no' => 'nullable|string|max:50',
        ]);

        // Generate guest code: G-XXXXX
        $lastGuest = Guest::orderBy('id', 'desc')->first();
        $nextId = $lastGuest ? $lastGuest->id + 1 : 1;
        $guestCode = 'G-'.sprintf('%05d', $nextId);

        Guest::create(array_merge($request->all(), [
            'guest_code' => $guestCode,
        ]));

        return redirect()->route('guests.index')->with('success', 'Guest registered successfully.');
    }

    public function edit(Guest $guest): View
    {
        return view('guests.edit', compact('guest'));
    }

    public function update(Request $request, Guest $guest): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'identity_type' => 'required|string',
            'identity_number' => 'required|string|max:50',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'profession' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'member_card_no' => 'nullable|string|max:50',
        ]);

        $guest->update($request->all());

        return redirect()->route('guests.index')->with('success', 'Guest details updated successfully.');
    }
}
