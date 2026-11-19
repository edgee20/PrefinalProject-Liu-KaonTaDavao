<?php

namespace App\Http\Controllers;

use App\Models\Eatery;
use Illuminate\View\View;

class EateryController extends Controller
{
    public function index(): View
    {
        return view('pages.pending', [
            'heading' => 'Find your next favorite.',
            'description' => 'A local directory of places to eat around Davao City.',
        ]);
    }

    public function create(): View
    {
        return view('pages.pending', [
            'heading' => 'Add an eatery',
            'description' => 'Make another neighborhood favorite part of the guide.',
        ]);
    }

    public function store(): void
    {
        abort(501, 'Eatery creation is not available yet.');
    }

    public function show(Eatery $eatery): View
    {
        return view('pages.pending', [
            'heading' => $eatery->name,
            'description' => 'Eatery details are being prepared.',
        ]);
    }

    public function edit(Eatery $eatery): View
    {
        return view('pages.pending', [
            'heading' => 'Edit '.$eatery->name,
            'description' => 'The eatery edit form is being prepared.',
        ]);
    }

    public function update(Eatery $eatery): void
    {
        abort(501, 'Eatery updates are not available yet.');
    }

    public function destroy(Eatery $eatery): void
    {
        abort(501, 'Eatery deletion is not available yet.');
    }
}
