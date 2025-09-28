<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
     public function index() {
        $user = User::find(Auth::id());


        return response()->json([
            'message' => 'User Profile Retrieved',
            'user' => $user,
        ]);
    }
}
