<header class="border-b border-line bg-paper">
    <div class="page-shell flex flex-col gap-6 py-6 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <p class="mb-2 text-xs font-semibold tracking-[0.18em] text-terracotta uppercase">A Davao City eatery guide</p>
            <a href="{{ route('home') }}" class="font-display text-2xl leading-tight text-forest sm:text-3xl">{{ config('app.name') }}</a>
        </div>
        <nav aria-label="Main navigation" class="flex flex-wrap items-center gap-x-6 gap-y-3 text-sm font-semibold">
            @foreach (['home' => 'Home', 'eateries.index' => 'Eateries', 'districts.index' => 'Districts', 'eateries.create' => 'Add Eatery'] as $name => $label)
                <a href="{{ route($name) }}"
                   @if (request()->routeIs($name)) aria-current="page" @endif
                   class="border-b-2 py-2 transition-colors hover:text-terracotta {{ request()->routeIs($name) ? 'border-terracotta text-forest' : 'border-transparent text-muted' }}">
                    {{ $label }}
                </a>
            @endforeach
        </nav>
    </div>
</header>
