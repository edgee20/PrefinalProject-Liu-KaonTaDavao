<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.pending', [
            'heading' => 'Good food, close to home.',
            'description' => 'Discover the carinderias, neighborhood kitchens, and everyday favorites of Davao City.',
        ]);
    }
}
