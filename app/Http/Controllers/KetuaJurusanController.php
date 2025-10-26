<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KetuaJurusanController extends Controller
{
    public function index()
    {
        return view('ketua.dashboard');
    }
}
