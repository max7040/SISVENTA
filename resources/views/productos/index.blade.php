<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Productos (Inventario)
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('partials.flash')

            <div class="mb-4">
                <a href="{{ route('productos.create') }}"
                   class="inline-block rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    + Nuevo Producto
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b">
                                <th class="py-2">ID</th>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Estado</th>
                                <th class="text-right">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($productos as $p)
                                <tr class="border-b">
                                    <td class="py-2">{{ $p->id }}</td>
                                    <td>{{ $p->nombre }}</td>
                                    <td>{{ $p->categoria }}</td>
                                    <td>S/ {{ number_format((float)$p->precio, 2) }}</td>
                                    <td>{{ $p->stock }}</td>

                                    <td>
                                        @if($p->activo)
                                            <span class="text-green-700 font-semibold">Activo</span>
                                        @else
                                            <span class="text-red-700 font-semibold">Inactivo</span>
                                        @endif
                                    </td>

                                    <td class="text-right space-x-2">
                                        <a class="text-indigo-600 hover:underline" href="{{ route('productos.show', $p) }}">Ver</a>
                                        <a class="text-yellow-600 hover:underline" href="{{ route('productos.edit', $p) }}">Editar</a>

                                        {{-- Activar / Desactivar --}}
                                        <form action="{{ route('productos.toggle', $p) }}" method="POST" class="inline"
                                              onsubmit="return confirm('¿Cambiar estado del producto?')">
                                            @csrf
                                            @method('PATCH')
                                            <button class="text-gray-700 hover:underline" type="submit">
                                                {{ $p->activo ? 'Desactivar' : 'Activar' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-4 text-center text-gray-500">
                                        No hay productos registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $productos->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
