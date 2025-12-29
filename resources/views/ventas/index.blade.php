<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Ventas (Historial)
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('partials.flash')

            <div class="mb-4">
                <a href="{{ route('ventas.create') }}"
                   class="inline-block rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    + Nueva Venta
                </a>
            </div>

            {{-- Filtros: Hoy / Fecha exacta --}}
            <form method="GET" class="flex flex-wrap gap-2 items-end mb-4">
                <a href="{{ route('ventas.index', ['hoy' => 1]) }}"
                   class="rounded bg-gray-800 px-4 py-2 text-white">
                    Hoy
                </a>

                <div>
                    <label class="block text-sm text-gray-600">Fecha</label>
                    <input type="date" name="fecha" value="{{ request('fecha') }}"
                           class="rounded border-gray-300">
                </div>

                <button class="rounded bg-blue-600 px-4 py-2 text-white">
                    Filtrar
                </button>

                <a href="{{ route('ventas.index') }}" class="text-gray-600 underline">
                    Limpiar
                </a>
            </form>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b">
                                <th class="py-2">ID</th>
                                <th>Comprobante</th>
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($ventas as $v)
                                <tr class="border-b">
                                    <td class="py-2">{{ $v->id }}</td>

                                    <td>
                                        {{ strtoupper($v->serie) }}-{{ str_pad((int)$v->numero, 6, '0', STR_PAD_LEFT) }}
                                    </td>

                                    <td>{{ $v->cliente?->nombre ?? 'Sin cliente' }}</td>

                                    <td>{{ $v->fecha?->format('Y-m-d H:i') }}</td>

                                    <td>S/ {{ number_format((float)$v->total, 2) }}</td>

                                    <td class="text-right">
                                        <a class="text-indigo-600 hover:underline"
                                           href="{{ route('ventas.show', $v) }}">
                                            Ver
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-4 text-center text-gray-500">
                                        No hay ventas registradas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $ventas->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
