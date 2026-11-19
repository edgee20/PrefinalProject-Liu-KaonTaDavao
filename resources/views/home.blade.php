@extends('layouts.app')

@section('content')
    <section class="grid gap-8 border-b border-line pb-10 lg:grid-cols-[1.4fr_1fr] lg:items-end lg:gap-16" aria-labelledby="home-heading">
        <div>
            <p class="mb-4 text-xs font-semibold tracking-[0.18em] text-terracotta uppercase">Kaon ta, Davao</p>
            <h1 id="home-heading" class="max-w-2xl font-display text-5xl leading-tight text-forest sm:text-6xl">Good food,<br>close to home.</h1>
            <p class="mt-5 max-w-xl text-lg leading-relaxed text-muted">From a bowl of tinola to your next suki spot, discover the carinderias and everyday kitchens that keep Davao fed.</p>
            <a href="{{ route('eateries.index') }}" class="button-primary mt-7">Explore the eateries <span aria-hidden="true">→</span></a>
        </div>
        <dl class="grid grid-cols-3 divide-x divide-line border-y border-line py-6">
            <div class="pr-3 text-center">
                <dd class="font-display text-3xl text-forest sm:text-4xl">{{ $eateryCount }}</dd>
                <dt class="mt-2 text-sm text-muted">Eateries</dt>
            </div>
            <div class="px-3 text-center">
                <dd class="font-display text-3xl text-forest sm:text-4xl">{{ $districtCount }}</dd>
                <dt class="mt-2 text-sm text-muted">Districts</dt>
            </div>
            <div class="pl-3 text-center">
                <dd class="font-display text-3xl text-forest sm:text-4xl">{{ $reviewCount }}</dd>
                <dt class="mt-2 text-sm text-muted">Reviews</dt>
            </div>
        </dl>
    </section>

    <section class="pt-10" aria-labelledby="top-rated-heading">
        <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="mb-2 text-xs font-semibold tracking-[0.16em] text-terracotta uppercase">From the community</p>
                <h2 id="top-rated-heading" class="font-display text-3xl text-forest">Top Rated</h2>
            </div>
            <a href="{{ route('eateries.index') }}" class="text-sm font-semibold text-forest underline underline-offset-4">View all eateries</a>
        </div>
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($topRated as $eatery)
                <x-eatery-card :eatery="$eatery" />
            @empty
                <p class="text-muted md:col-span-2 lg:col-span-3">No rated eateries yet. Explore the guide and share your first review.</p>
            @endforelse
        </div>
    </section>
@endsection
