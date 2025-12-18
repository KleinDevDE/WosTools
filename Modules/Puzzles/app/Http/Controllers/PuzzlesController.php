<?php

namespace Modules\Puzzles\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PuzzlesController extends Controller
{
    public function list()
    {
        return view('puzzles::albums');
    }
}
