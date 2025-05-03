<?php

namespace App\Http\Controllers;

class AdminController extends Controller
{
    public function index()
    {
        return response()->json([
            'msg' => 'Hello Admin',
            'user' => auth()->user(),
        ]);
    }
}
