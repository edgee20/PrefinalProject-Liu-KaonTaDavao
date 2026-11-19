<?php

namespace App\Http\Controllers;

use App\Models\District;
use Illuminate\View\View;

class DistrictController extends Controller
{
    public function index(): View
    {
        return view('pages.pending', [
            'heading' => 'Explore by district',
            'description' => 'From Poblacion to Calinan, find a table in your neighborhood.',
        ]);
    }

    public function show(District $district): View
    {
        return view('pages.pending', [
            'heading' => $district->name,
            'description' => 'The district guide is being prepared.',
        ]);
    }
}
