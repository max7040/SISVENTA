<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Producto #{{ $producto->id }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @include('partials.flash')

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p><b>Nombre:</b> {{ $producto->nombre }}</p>
                <p><b>Categoría:</b> {{ $producto->categoria }}</p>
                <p><b>Precio:</b> S/ {{ number_format((float)$producto->precio, 2) }}</p>
                <p><b>Stock:</b> {{ $producto->stock }}</p>

                <div class="mt-4 flex gap-2">
                    <a href="{{ route('productos.index') }}" class="rounded bg-gray-200 px-4 py-2">Volver</a>
                    <a href="{{ route('productos.edit', $producto) }}" class="rounded bg-yellow-500 px-4 py-2 text-white">Editar</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
