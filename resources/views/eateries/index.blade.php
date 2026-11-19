@extends('layouts.app')

@section('content')
    <header class="mb-8">
        <p class="mb-3 text-xs font-semibold tracking-[0.18em] text-terracotta uppercase">The neighborhood directory</p>
        <h1 class="font-display text-4xl text-forest sm:text-5xl">Find your next favorite.</h1>
        <p class="mt-4 max-w-2xl leading-relaxed text-muted">Browse Davao’s everyday kitchens. Search a dish, pick a district, or find the features that make a place feel like your suki spot.</p>
    </header>

    <form method="GET" action="{{ route('eateries.index') }}" class="mb-8 grid gap-4 border-y border-line py-6 sm:grid-cols-2 lg:grid-cols-[1.4fr_1fr_1fr_auto] lg:items-end">
        <div>
            <label for="q" class="field-label">Search eateries or specialties</label>
            <input id="q" name="q" type="search" value="{{ request('q') }}" placeholder="Try tinola or a favorite eatery" class="form-input">
        </div>
        <div>
            <label for="district" class="field-label">District</label>
            <select id="district" name="district" class="form-input">
                <option value="">All districts</option>
                @foreach ($districts as $district)
                    <option value="{{ $district->id }}" @selected((string) request('district') === (string) $district->id)>{{ $district->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="feature" class="field-label">Feature</label>
            <select id="feature" name="feature" class="form-input">
                <option value="">All features</option>
                @foreach ($features as $feature)
                    <option value="{{ $feature->name }}" @selected(request('feature') === $feature->name)>{{ $feature->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-center gap-4">
            <button type="submit" class="button-primary">Find eateries</button>
            <a href="{{ route('eateries.index') }}" class="text-sm text-muted underline underline-offset-4">Reset</a>
        </div>
    </form>

    <p class="mb-5 text-sm text-muted">{{ $eateries->total() }} {{ $eateries->total() === 1 ? 'eatery' : 'eateries' }} found</p>
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($eateries as $eatery)
            <x-eatery-card :eatery="$eatery" />
        @empty
            <p class="border-y border-line py-10 text-muted md:col-span-2 lg:col-span-3">No eateries match your filters.</p>
        @endforelse
    </div>
    <div class="mt-8">{{ $eateries->links() }}</div>
@endsection
