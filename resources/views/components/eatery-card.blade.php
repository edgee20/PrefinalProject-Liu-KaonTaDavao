@props(['eatery'])

<article class="flex h-full flex-col border border-line bg-paper p-6">
    <div class="mb-4 flex items-center justify-between gap-3 text-sm">
        <a href="{{ route('districts.show', $eatery->district) }}" class="font-medium text-muted underline-offset-4 hover:text-forest hover:underline">{{ $eatery->district->name }}</a>
        <span class="font-semibold text-forest" aria-label="{{ ucfirst($eatery->price_range) }} price range">{{ ['budget' => '₱', 'mid' => '₱₱', 'splurge' => '₱₱₱'][$eatery->price_range] }}</span>
    </div>
    <h3 class="font-display text-2xl leading-snug text-forest">
        <a href="{{ route('eateries.show', $eatery) }}" class="underline-offset-4 hover:underline">{{ $eatery->name }}</a>
    </h3>
    <p class="mt-3 text-sm leading-relaxed text-muted">{{ $eatery->specialty ?? 'Specialty not listed.' }}</p>
    <div class="mt-5 flex flex-wrap gap-2">
        @foreach ($eatery->features as $feature)
            <a href="{{ route('eateries.index', ['feature' => $feature->name]) }}" class="border border-line px-2 py-1 text-xs text-forest hover:border-forest">{{ $feature->name }}</a>
        @endforeach
    </div>
    <div class="mt-auto pt-6"><div class="flex flex-wrap items-center justify-between gap-3 border-t border-line pt-4">
        <x-rating :rating="$eatery->reviews_avg_rating" :count="$eatery->reviews_count" />
        @if (! $eatery->is_open)
            <span class="border border-terracotta px-2 py-1 text-xs font-semibold text-terracotta">Closed</span>
        @endif
    </div></div>
</article>
