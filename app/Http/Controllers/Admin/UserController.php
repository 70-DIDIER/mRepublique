<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index()
    {
        // Logique pour afficher la liste des utilisateurs
        // Par exemple, retourner tous les utilisateurs
        $users = User::all();
        return view('users.index', compact('users'));
    } 
   
}
