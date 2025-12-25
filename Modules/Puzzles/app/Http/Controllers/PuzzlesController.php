<?php

namespace Modules\Puzzles\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PuzzlesController extends Controller
{
    public function albums()
    {
        return view('puzzles::albums');
    }

    public function puzzles()
    {
        return view('puzzles::puzzles');
    }

    public function pieces()
    {
        return view('puzzles::puzzles');
    }
}
