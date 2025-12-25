<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class MediaController extends Controller
{
    public function gallery(): View
    {
        return view('media.gallery');
    }
}
