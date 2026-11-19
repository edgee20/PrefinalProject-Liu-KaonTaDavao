@extends('layouts.app')

@section('content')
    <div class="max-w-4xl">
        <h1 class="font-display text-4xl text-forest">Add an eatery</h1>
        <p class="mt-3 mb-8 text-muted">Put another neighborhood favorite on the map.</p>
        <form method="POST" action="{{ route('eateries.store') }}" novalidate>
            @csrf
            @include('eateries._form', ['submitLabel' => 'Add eatery'])
        </form>
    </div>
@endsection
