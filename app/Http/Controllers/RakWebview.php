<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RakWebview extends Controller
{
    public function index() {
        return view('pages.rak.dashboard');
    }
}
