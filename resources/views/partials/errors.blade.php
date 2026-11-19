@if ($errors->any())
    <div role="alert" class="mb-6 border-l-4 border-red-700 bg-paper p-5 text-sm text-red-800">
        <p class="font-semibold">Please check the following:</p>
        <ul class="mt-2 list-disc space-y-1 pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
