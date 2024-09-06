<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NeracaController extends Controller
{
    public function index()
    {
        $title = 'Laporan Neraca Keuangan'; 
        return view('admin.laporan.neraca.index', compact('title')); 
    }
}
