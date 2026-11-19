<?php

namespace App\Http\Requests;

use App\Models\Eatery;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreEateryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', 'unique:eateries,name', function (string $attribute, mixed $value, Closure $fail) {
                if (! is_string($value)) {
                    return;
                }
                $slug = Str::slug($value);
                if ($slug === '' || $slug === 'create' || Eatery::where('slug', $slug)->exists()) {
                    $fail('Choose a name that produces a unique eatery URL.');
                }
            }],
            'district_id' => ['required', 'exists:districts,id'],
            'address' => ['required', 'string', 'max:255'],
            'price_range' => ['required', 'in:budget,mid,splurge'],
            'specialty' => ['nullable', 'string', 'max:100'],
            'is_open' => ['boolean'],
            'features' => ['required', 'array', 'min:1'],
            'features.*' => ['exists:features,id'],
            'permit_no' => ['required', 'regex:/^DVO-\d{4}-\d{4,5}$/', 'unique:permits,permit_no'],
            'expires_on' => ['required', 'date', 'after:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Kaon Ta Liu already lists an eatery with this name.',
            'features.required' => 'Pick at least one feature.',
            'permit_no.regex' => 'Permit number must look like DVO-2026-0415.',
            'expires_on.after' => 'The permit must not be expired.',
        ];
    }
}
