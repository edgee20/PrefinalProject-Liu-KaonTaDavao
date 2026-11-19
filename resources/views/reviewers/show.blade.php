@extends('layouts.app')

@section('content')
    <header class="mb-8">
        <p class="mb-3 text-xs font-semibold tracking-widest text-terracotta uppercase">Community food notes</p>
        <h1 class="font-display text-4xl text-forest sm:text-5xl">{{ $user->name }}</h1>
        <p class="mt-4 text-muted">{{ $user->reviews_count }} reviews written</p>
    </header>
    <div class="max-w-3xl space-y-5">
        @forelse ($user->reviews as $review)
            <article class="border border-line bg-paper p-6">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="font-display text-2xl text-forest"><a href="{{ route('eateries.show', $review->eatery) }}" class="underline-offset-4 hover:underline">{{ $review->eatery->name }}</a></h2>
                        <p class="mt-2 text-xs text-muted"><time datetime="{{ $review->created_at->toDateString() }}">{{ $review->created_at->format('M j, Y') }}</time></p>
                    </div>
                    <x-rating :rating="$review->rating" />
                </div>
                <p class="mt-4 whitespace-pre-line text-sm leading-relaxed">{{ $review->body }}</p>
            </article>
        @empty
            <p class="text-muted">No reviews yet</p>
        @endforelse
    </div>
@endsection
