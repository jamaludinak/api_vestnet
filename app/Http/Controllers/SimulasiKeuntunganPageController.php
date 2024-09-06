<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SimulasiKeuntunganPageController extends Controller
{
    public function index()
    {
        $title = 'simulasi';
        return view('user.simulasiuntung', compact('title')); 
    }
}
