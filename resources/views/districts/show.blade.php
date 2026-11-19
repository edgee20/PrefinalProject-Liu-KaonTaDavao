@extends('layouts.app')

@section('content')
    <header class="mb-8">
        <a href="{{ route('districts.index') }}" class="text-sm text-muted underline underline-offset-4">All districts</a>
        <h1 class="mt-4 font-display text-4xl text-forest sm:text-5xl">{{ $district->name }}</h1>
        <p class="mt-4 text-muted">{{ $district->reviews_count }} reviews in {{ $district->name }}</p>
    </header>
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($eateries as $eatery)
            <x-eatery-card :eatery="$eatery" />
        @empty
            <p class="text-muted">No eateries listed in this district yet.</p>
        @endforelse
    </div>
@endsection
