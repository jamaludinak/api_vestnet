<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DownloadPageController extends Controller
{
    public function index()
    {
        $title = 'download';
        return view('user.download', compact('title')); 
    }
}
