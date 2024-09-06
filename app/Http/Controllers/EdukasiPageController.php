<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EdukasiPageController extends Controller
{
    public function index()
    {
        $title = 'edukasi';
        return view('user.edukasi', compact('title')); 
    }

    public function index2()
    {
        $title = 'edukasi';
        return view('user.edukasi2', compact('title')); 
    }

    public function index3()
    {
        $title = 'edukasi';
        return view('user.edukasi3', compact('title')); 
    }
}
