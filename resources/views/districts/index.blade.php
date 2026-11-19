@extends('layouts.app')

@section('content')
    <header class="mb-8">
        <p class="mb-3 text-xs font-semibold tracking-widest text-terracotta uppercase">Around the city</p>
        <h1 class="font-display text-4xl text-forest sm:text-5xl">Explore by district</h1>
        <p class="mt-4 text-muted">Find your next table, from Poblacion to Calinan.</p>
    </header>
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($districts as $district)
            <a href="{{ route('districts.show', $district) }}" class="border border-line bg-paper p-6 hover:border-forest">
                <h2 class="font-display text-2xl text-forest">{{ $district->name }}</h2>
                <p class="mt-3 text-sm text-muted">{{ $district->eateries_count }} {{ $district->eateries_count === 1 ? 'eatery' : 'eateries' }}</p>
            </a>
        @endforeach
    </div>
@endsection
