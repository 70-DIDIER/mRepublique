<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LivreurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $livreurs = User::all();
        return view('livreurs.index', compact('livreurs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('livreurs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function edit(string $id)
    {
        //
        $livreur = User::findOrFail($id);
        return view('livreurs.update', compact('livreur'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id){
    $livreur = User::findOrFail($id);

    $request->validate([
        'name' => 'required|string',
        'email' => 'required|email|unique:users,email,' . $livreur->id,
        'password' => 'nullable|string', // plus confirmé sauf si tu ajoutes confirmation
        'role' => 'required|in:livreur',
        // 'adresse' => 'nullable|string',
        'telephone' => 'nullable|string|unique:users,telephone,' . $livreur->id,
        // 'photo' => 'nullable|image|max:2048',
    ]);

    $livreur->name = $request->name;
    $livreur->email = $request->email;
    if ($request->filled('password')) {
        $livreur->password = Hash::make($request->password);
    }
    $livreur->role = $request->role;
    // $livreur->adresse = $request->adresse;
    $livreur->telephone = $request->telephone;

    /* if ($request->hasFile('photo')) {
        $photoPath = $request->file('photo')->store('photos', 'public');
        $livreur->photo = $photoPath;
    } */

    $livreur->save();

    return redirect()->route('livreurs.index')->with('success', 'Livreur mis à jour avec succès.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $livreur = User::findOrFail($id);
        $livreur->delete();

        return redirect()->route('livreurs.index')->with('success', 'livreur supprimé avec succès');
    }
}
