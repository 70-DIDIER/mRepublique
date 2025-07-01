<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class LivreurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $livreurs = User::where('role', 'livreur')->get();
        return view('livreur.index', compact('livreurs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('livreur.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|',
        ]);

        // Create the livreur user
        $livreur = new User();
        $livreur->name = $request->name;
        $livreur->email = $request->email;
        $livreur->password = Hash::make($request->password);
        $livreur->role = 'livreur';
        $livreur->save();

        return redirect()->route('livreur.index')->with('success', 'Livreur created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $livreur)
    {
        return view('livreur.edit', compact('livreur'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $livreur)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $livreur->id,
            'password' => 'nullable|string|min:8',
        ]);

        $livreur->name = $request->name;
        $livreur->email = $request->email;

        if ($request->filled('password')) {
            $livreur->password = Hash::make($request->password);
        }

        $livreur->save();

        return redirect()->route('livreur.index')->with('success', 'Livreur updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $livreur)
    {
        $livreur->delete();
        return redirect()->route('livreur.index')->with('success', 'Livreur deleted successfully.');
    }
}
