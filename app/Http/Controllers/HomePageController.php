<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProyekModel;

class HomePageController extends Controller
{
    public function index()
    {
        $title = 'home';
        $projects = ProyekModel::all(); 
        return view('user.home', compact('title', 'projects')); 
    }
}
