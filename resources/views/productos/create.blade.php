<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nuevo Producto
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @include('partials.flash')

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('productos.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block mb-1">Nombre</label>
                        <input name="nombre" class="w-full rounded border-gray-300"
                               value="{{ old('nombre') }}" required>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1">Categoría</label>
                        <select name="categoria" class="w-full rounded border-gray-300" required>
                            @foreach ($categorias as $c)
                                <option value="{{ $c }}" @selected(old('categoria') === $c)>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1">Precio</label>
                        <input type="number" step="0.01" min="0" name="precio"
                               class="w-full rounded border-gray-300"
                               value="{{ old('precio', 0) }}" required>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1">Stock</label>
                        <input type="number" min="0" name="stock"
                               class="w-full rounded border-gray-300"
                               value="{{ old('stock', 0) }}" required>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('productos.index') }}" class="rounded bg-gray-200 px-4 py-2">
                            Volver
                        </a>
                        <button class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
