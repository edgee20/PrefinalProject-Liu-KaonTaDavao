@extends('layouts.app')

@section('content')
    <div class="max-w-4xl">
        <h1 class="font-display text-4xl text-forest">Edit {{ $eatery->name }}</h1>
        <p class="mt-3 mb-8 text-muted">Keep the guide’s details up to date.</p>
        <form method="POST" action="{{ route('eateries.update', $eatery) }}" novalidate>
            @csrf
            @method('PUT')
            @include('eateries._form', ['submitLabel' => 'Save changes'])
        </form>
    </div>
@endsection
