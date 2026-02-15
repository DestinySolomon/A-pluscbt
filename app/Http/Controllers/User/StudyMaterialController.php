<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StudyMaterialController extends Controller
{
    /**
     * Display study materials page.
     */
    public function index()
    {
        return view('user.materials.index');
    }
}