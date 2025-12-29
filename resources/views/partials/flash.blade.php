@if (session('success'))
    <div class="mb-4 rounded-lg bg-green-100 p-3 text-green-800">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-4 rounded-lg bg-red-100 p-3 text-red-800">
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-4 rounded-lg bg-red-100 p-3 text-red-800">
        <p class="font-semibold">Corrige lo siguiente:</p>
        <ul class="list-disc ms-5">
            @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif
