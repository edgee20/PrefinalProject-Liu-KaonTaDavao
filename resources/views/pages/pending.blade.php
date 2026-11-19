@extends('layouts.app')

@section('content')
    <section aria-labelledby="page-heading">
        <p class="mb-4 text-xs font-semibold tracking-[0.18em] text-terracotta uppercase">Kaon ta, Davao</p>
        <h1 id="page-heading" class="max-w-3xl font-display text-4xl leading-tight text-forest sm:text-5xl lg:text-6xl">{{ $heading }}</h1>
        <p class="mt-5 max-w-2xl text-lg leading-relaxed text-muted">{{ $description }}</p>
        <div class="mt-10 max-w-2xl border-t border-line pt-6">
            <p class="text-sm font-semibold text-forest">This page is in progress.</p>
            <p class="mt-2 text-sm leading-relaxed text-muted">More of the guide will be available soon.</p>
        </div>
    </section>
@endsection
