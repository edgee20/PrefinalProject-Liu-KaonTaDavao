@if (session('status'))
    <div role="status" class="mb-8 border-l-4 border-forest bg-paper px-5 py-4 text-sm text-forest">
        {{ session('status') }}
    </div>
@endif
