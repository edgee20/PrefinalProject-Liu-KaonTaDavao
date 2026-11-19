<?php

namespace App\Http\Controllers;

use App\Models\Eatery;
use App\Models\Review;

class ReviewController extends Controller
{
    public function store(Eatery $eatery): void
    {
        abort(501, 'Review submission is not available yet.');
    }

    public function destroy(Review $review): void
    {
        abort(501, 'Review deletion is not available yet.');
    }
}
