@extends('layouts.app')

@section('content')
    <header class="mb-9 border-b border-line pb-8">
        <a href="{{ route('districts.show', $eatery->district) }}" class="text-sm font-semibold text-terracotta underline underline-offset-4">{{ $eatery->district->name }}</a>
        <div class="mt-4 flex flex-wrap items-start justify-between gap-5">
            <div>
                <h1 class="font-display text-4xl text-forest sm:text-5xl">{{ $eatery->name }}</h1>
                <p class="mt-3 text-muted">{{ $eatery->address }}</p>
                <div class="mt-4 flex flex-wrap items-center gap-4">
                    <span class="font-semibold text-forest">{{ ['budget' => '₱', 'mid' => '₱₱', 'splurge' => '₱₱₱'][$eatery->price_range] }}</span>
                    <x-rating :rating="$eatery->reviews_avg_rating" :count="$eatery->reviews_count" />
                    <span class="text-sm font-semibold {{ $eatery->is_open ? 'text-forest' : 'text-red-700' }}">{{ $eatery->is_open ? 'Open' : 'Closed' }}</span>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('eateries.edit', $eatery) }}" class="button-secondary">Edit eatery</a>
                <form method="POST" action="{{ route('eateries.destroy', $eatery) }}" data-confirm="Delete this eatery and all of its dishes, permit, and reviews?">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm font-semibold text-red-700 underline underline-offset-4">Delete eatery</button>
                </form>
            </div>
        </div>
        <div class="mt-5 flex flex-wrap gap-2">
            @foreach ($eatery->features as $feature)
                <a href="{{ route('eateries.index', ['feature' => $feature->name]) }}" class="border border-line bg-paper px-3 py-1.5 text-xs text-forest hover:border-forest">{{ $feature->name }}</a>
            @endforeach
        </div>
    </header>

    <div class="grid items-start gap-8 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)]">
        <div class="min-w-0 space-y-10">
            <section aria-labelledby="menu-heading">
                <p class="mb-2 text-xs font-semibold tracking-widest text-terracotta uppercase">On the table</p>
                <h2 id="menu-heading" class="font-display text-3xl text-forest">Dishes &amp; specialties</h2>
                <p class="mt-3 text-muted">Specialty: {{ $eatery->specialty ?? 'Not listed' }}</p>
                <ul class="mt-5 divide-y divide-line border-y border-line">
                    @forelse ($eatery->dishes as $dish)
                        <li class="flex items-center justify-between gap-4 py-4">
                            <div>
                                <span class="font-medium">{{ $dish->name }}</span>
                                @if ($dish->is_bestseller)
                                    <span class="ml-2 text-xs font-semibold text-terracotta">Bestseller</span>
                                @endif
                            </div>
                            <span class="shrink-0 font-semibold text-forest">₱{{ number_format($dish->price, 2) }}</span>
                        </li>
                    @empty
                        <li class="py-5 text-sm text-muted">No dishes listed yet.</li>
                    @endforelse
                </ul>
            </section>

            <section aria-labelledby="reviews-heading">
                <h2 id="reviews-heading" class="font-display text-3xl text-forest">Reviews <span class="text-xl text-muted">({{ $eatery->reviews_count }})</span></h2>
                <div class="mt-5 space-y-5">
                    @forelse ($eatery->reviews as $review)
                        <article class="border border-line bg-paper p-5">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <a href="{{ route('reviewers.show', $review->user) }}" class="font-semibold text-forest underline underline-offset-4">{{ $review->user->name }}</a>
                                    <p class="mt-1 text-xs text-muted"><time datetime="{{ $review->created_at->toDateString() }}">{{ $review->created_at->format('M j, Y') }}</time></p>
                                </div>
                                <x-rating :rating="$review->rating" />
                            </div>
                            <p class="mt-4 whitespace-pre-line text-sm leading-relaxed">{{ $review->body }}</p>
                            <form method="POST" action="{{ route('reviews.destroy', $review) }}" class="mt-4" data-confirm="Delete this review?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-semibold text-red-700 underline underline-offset-4">Delete review</button>
                            </form>
                        </article>
                    @empty
                        <p class="text-sm text-muted">No reviews yet</p>
                    @endforelse
                </div>
            </section>

            <section aria-labelledby="review-form-heading" class="border-t border-line pt-8">
                @if ($eatery->is_open)
                    <h2 id="review-form-heading" class="mb-2 font-display text-3xl text-forest">How was your meal?</h2>
                    <p class="mb-6 text-sm text-muted">Share a food note with the neighborhood.</p>
                    <form method="POST" action="{{ route('eateries.reviews.store', $eatery) }}" novalidate class="space-y-5">
                        @csrf
                        @include('partials.errors')
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="user_id" class="field-label">Reviewer</label>
                                <select id="user_id" name="user_id" required class="form-input">
                                    <option value="">Choose a reviewer</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" @selected((string) old('user_id') === (string) $user->id)>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @error('user_id') <p class="field-error">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="rating" class="field-label">Rating</label>
                                <select id="rating" name="rating" required class="form-input">
                                    <option value="">Choose a rating</option>
                                    @foreach ([5, 4, 3, 2, 1] as $rating)
                                        <option value="{{ $rating }}" @selected((string) old('rating') === (string) $rating)>{{ $rating }} {{ $rating === 1 ? 'star' : 'stars' }}</option>
                                    @endforeach
                                </select>
                                @error('rating') <p class="field-error">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div>
                            <label for="body" class="field-label">Your review</label>
                            <textarea id="body" name="body" rows="4" minlength="10" maxlength="1000" required class="form-input" aria-describedby="review-hint">{{ old('body') }}</textarea>
                            <p id="review-hint" class="mt-2 text-xs text-muted">10–1,000 characters.</p>
                            @error('body') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                        <button type="submit" class="button-primary">Post review</button>
                    </form>
                @else
                    <p class="border-l-4 border-terracotta bg-paper p-5 text-sm text-forest">This eatery is closed. Reviews are paused.</p>
                @endif
            </section>
        </div>

        <aside class="border border-line bg-paper p-6" aria-labelledby="permit-heading">
            <p class="mb-2 text-xs font-semibold tracking-widest text-terracotta uppercase">The details</p>
            <h2 id="permit-heading" class="font-display text-2xl text-forest">Business permit</h2>
            @if ($eatery->permit)
                <dl class="mt-5 space-y-4 text-sm">
                    <div><dt class="text-muted">Permit number</dt><dd class="mt-1 font-semibold">{{ $eatery->permit->permit_no }}</dd></div>
                    <div><dt class="text-muted">Expires on</dt><dd class="mt-1 font-semibold">{{ $eatery->permit->expires_on->format('F j, Y') }}</dd></div>
                </dl>
                @if ($eatery->permit->expires_on->lt(today()))
                    <p class="mt-5 font-semibold text-red-700">Permit expired</p>
                @endif
            @else
                <p class="mt-4 text-sm text-muted">No permit listed.</p>
            @endif
        </aside>
    </div>
@endsection
