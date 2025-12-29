<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Cliente #{{ $cliente->id }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @include('partials.flash')

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p><b>Nombre:</b> {{ $cliente->nombre }}</p>
                <p><b>DNI/RUC:</b> {{ $cliente->dni_ruc ?? '-' }}</p>
                <p><b>Teléfono:</b> {{ $cliente->telefono ?? '-' }}</p>

                <div class="mt-4 flex gap-2">
                    <a href="{{ route('clientes.index') }}" class="rounded bg-gray-200 px-4 py-2">Volver</a>
                    <a href="{{ route('clientes.edit', $cliente) }}" class="rounded bg-yellow-500 px-4 py-2 text-white">Editar</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
