<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEateryRequest;
use App\Http\Requests\UpdateEateryRequest;
use App\Models\District;
use App\Models\Eatery;
use App\Models\Feature;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
        $eatery = new Eatery;
        $eatery->setRelation('permit', null)->setRelation('features', collect());

        return view('eateries.create', [
            'eatery' => $eatery,
            'districts' => District::orderBy('name')->get(),
            'features' => Feature::orderBy('name')->get(),
        ]);
    }

    public function store(StoreEateryRequest $request): RedirectResponse
    {
        $fields = $request->validated();
        $featureIds = $fields['features'];
        $permit = ['permit_no' => $fields['permit_no'], 'expires_on' => $fields['expires_on']];
        unset($fields['features'], $fields['permit_no'], $fields['expires_on']);
        $fields['slug'] = Str::slug($fields['name']);
        $fields['is_open'] = (bool) ($fields['is_open'] ?? false);

        $eatery = DB::transaction(function () use ($fields, $permit, $featureIds) {
            $eatery = Eatery::create($fields);
            $eatery->permit()->create($permit);
            $eatery->features()->sync($featureIds);

            return $eatery;
        });

        return redirect()->route('eateries.show', $eatery)->with('status', 'Eatery added.');
    }

    public function show(Eatery $eatery): View
    {
        $eatery->load([
            'district', 'features', 'permit',
            'dishes' => fn ($query) => $query->orderByDesc('is_bestseller')->orderBy('name'),
            'reviews' => fn ($query) => $query->with('user')->latest()->orderByDesc('id'),
        ])->loadCount('reviews')->loadAvg('reviews', 'rating');

        return view('eateries.show', [
            'eatery' => $eatery,
            'users' => $eatery->is_open ? User::orderBy('name')->get(['id', 'name']) : collect(),
        ]);
    }

    public function edit(Eatery $eatery): View
    {
        $eatery->load(['permit', 'features']);

        return view('eateries.edit', [
            'eatery' => $eatery,
            'districts' => District::orderBy('name')->get(),
            'features' => Feature::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateEateryRequest $request, Eatery $eatery): RedirectResponse
    {
        $fields = $request->validated();
        $featureIds = $fields['features'];
        $permit = ['permit_no' => $fields['permit_no'], 'expires_on' => $fields['expires_on']];
        unset($fields['features'], $fields['permit_no'], $fields['expires_on']);
        $fields['slug'] = Str::slug($fields['name']);
        $fields['is_open'] = (bool) ($fields['is_open'] ?? false);

        DB::transaction(function () use ($eatery, $fields, $permit, $featureIds) {
            $eatery->update($fields);
            $eatery->permit()->update($permit);
            $eatery->features()->sync($featureIds);
        });

        return redirect()->route('eateries.show', $eatery)->with('status', 'Eatery updated.');
    }

    public function destroy(Eatery $eatery): RedirectResponse
    {
        DB::transaction(function () use ($eatery) {
            $eatery->permit()->delete();
            $eatery->dishes()->delete();
            $eatery->reviews()->delete();
            $eatery->features()->detach();
            $eatery->delete();
        });

        return redirect()->route('eateries.index')->with('status', 'Eatery deleted.');
    }
}
