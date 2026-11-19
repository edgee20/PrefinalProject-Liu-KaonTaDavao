@include('partials.errors')

@php
    $selectedFeatures = (array) old('features', session()->hasOldInput() ? [] : $eatery->features->pluck('id')->all());
@endphp

<section class="border border-line bg-paper p-5 sm:p-7" aria-labelledby="eatery-information">
    <h2 id="eatery-information" class="mb-6 font-display text-2xl text-forest">Eatery information</h2>
    <div class="grid gap-6 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label for="name" class="field-label">Eatery name</label>
            <input id="name" name="name" value="{{ old('name', $eatery->name) }}" maxlength="100" required class="form-input" @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
            @error('name') <p id="name-error" class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="district_id" class="field-label">District</label>
            <select id="district_id" name="district_id" required class="form-input">
                <option value="">Choose a district</option>
                @foreach ($districts as $district)
                    <option value="{{ $district->id }}" @selected((string) old('district_id', $eatery->district_id) === (string) $district->id)>{{ $district->name }}</option>
                @endforeach
            </select>
            @error('district_id') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="price_range" class="field-label">Price range</label>
            <select id="price_range" name="price_range" required class="form-input">
                @foreach (['budget' => '₱ · Budget', 'mid' => '₱₱ · Mid', 'splurge' => '₱₱₱ · Splurge'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('price_range', $eatery->price_range ?? 'budget') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('price_range') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div class="sm:col-span-2">
            <label for="address" class="field-label">Address</label>
            <input id="address" name="address" value="{{ old('address', $eatery->address) }}" maxlength="255" required class="form-input">
            @error('address') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div class="sm:col-span-2">
            <label for="specialty" class="field-label">Specialty <span class="font-normal text-muted">(optional)</span></label>
            <input id="specialty" name="specialty" value="{{ old('specialty', $eatery->specialty) }}" maxlength="100" class="form-input">
            @error('specialty') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div class="sm:col-span-2">
            <input type="hidden" name="is_open" value="0">
            <label for="is_open" class="flex items-center gap-3 text-sm font-semibold text-forest">
                <input id="is_open" name="is_open" type="checkbox" value="1" @checked(old('is_open', $eatery->exists ? $eatery->is_open : true)) class="size-4 accent-forest">
                Open for business
            </label>
            @error('is_open') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <fieldset class="sm:col-span-2">
            <legend class="field-label">Features <span class="font-normal text-muted">(choose at least one)</span></legend>
            <div class="grid gap-3 sm:grid-cols-2">
                @foreach ($features as $feature)
                    <label for="feature-{{ $feature->id }}" class="flex items-center gap-3 text-sm">
                        <input id="feature-{{ $feature->id }}" name="features[]" type="checkbox" value="{{ $feature->id }}" @checked(in_array((string) $feature->id, array_map('strval', $selectedFeatures), true)) class="size-4 accent-forest">
                        {{ $feature->name }}
                    </label>
                @endforeach
            </div>
            @error('features') <p class="field-error">{{ $message }}</p> @enderror
            @error('features.*') <p class="field-error">{{ $message }}</p> @enderror
        </fieldset>
    </div>
</section>

<section class="mt-6 border border-line bg-paper p-5 sm:p-7" aria-labelledby="permit-information">
    <h2 id="permit-information" class="mb-6 font-display text-2xl text-forest">Business permit</h2>
    <div class="grid gap-6 sm:grid-cols-2">
        <div>
            <label for="permit_no" class="field-label">Permit number</label>
            <input id="permit_no" name="permit_no" value="{{ old('permit_no', $eatery->permit?->permit_no) }}" placeholder="DVO-2026-0415" required class="form-input">
            @error('permit_no') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="expires_on" class="field-label">Permit expiry date</label>
            <input id="expires_on" name="expires_on" type="date" value="{{ old('expires_on', $eatery->permit?->expires_on?->format('Y-m-d')) }}" required class="form-input">
            @error('expires_on') <p class="field-error">{{ $message }}</p> @enderror
        </div>
    </div>
</section>

<div class="mt-7 flex flex-wrap items-center gap-5">
    <button type="submit" class="button-primary">{{ $submitLabel }}</button>
    <a href="{{ $eatery->exists ? route('eateries.show', $eatery) : route('eateries.index') }}" class="text-sm text-muted underline underline-offset-4">Cancel</a>
</div>
