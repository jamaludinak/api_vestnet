<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LabaRugiController extends Controller
{
    public function index()
    {
        $title = 'Laporan Laba Rugi'; 
        return view('admin.laporan.laba-rugi.index', compact('title')); 
    }
}
