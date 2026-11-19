<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class ReviewerController extends Controller
{
    public function show(User $user): View
    {
        return view('pages.pending', [
            'heading' => $user->name,
            'description' => 'This reviewer’s food notes are being prepared.',
        ]);
    }
}
