<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TentangKamiPageController extends Controller
{
    public function index()
    {
        $title = 'tentang';
        return view('user.tentangkami', compact('title')); 
    }
}
