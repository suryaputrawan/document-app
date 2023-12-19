<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    function index()
    {
        $doctors = Doctor::orderBy('name')->get()
            ->groupBy('specialist.name');

        return view('front', [
            'doctors'     => $doctors
        ]);
    }
}
