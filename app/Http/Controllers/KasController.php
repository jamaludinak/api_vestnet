<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KasController extends Controller
{
    public function index()
    {
        $title = 'Laporan Arus Kas'; 
        return view('admin.laporan.kas.index', compact('title')); 
    }
}
