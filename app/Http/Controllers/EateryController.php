<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Eatery;
use App\Models\Feature;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EateryController extends Controller
{
    public function index(Request $request): View
    {
        $eateries = Eatery::with(['district', 'features'])
            ->withCount('reviews')->withAvg('reviews', 'rating')
            ->when($request->filled('q'), function ($query) use ($request) {
                $query->where(function ($search) use ($request) {
                    $search->where('name', 'like', '%'.$request->input('q').'%')
                        ->orWhere('specialty', 'like', '%'.$request->input('q').'%');
                });
            })
            ->when($request->filled('district'), fn ($query) => $query->where('district_id', $request->input('district')))
            ->when($request->filled('feature'), function ($query) use ($request) {
                $query->whereHas('features', fn ($features) => $features->where('name', $request->input('feature')));
            })
            ->orderBy('name')->paginate(6)->withQueryString();

        return view('eateries.index', [
            'eateries' => $eateries,
            'districts' => District::orderBy('name')->get(),
            'features' => Feature::orderBy('name')->get(),
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
