@props(['rating' => null, 'count' => null])

@if ($rating === null || $count === 0)
    <span class="text-sm text-muted">No reviews yet</span>
@else
    <span class="text-sm text-forest">
        <span aria-label="{{ number_format($rating, 1) }} out of 5 stars">{{ number_format($rating, 1) }} <span aria-hidden="true" class="text-terracotta">★</span></span>@if ($count !== null) <span class="text-muted">({{ $count }} {{ $count === 1 ? 'review' : 'reviews' }})</span>@endif
    </span>
@endif
