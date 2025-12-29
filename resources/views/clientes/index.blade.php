<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Clientes
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('partials.flash')

            <div class="mb-4">
                <a href="{{ route('clientes.create') }}"
                   class="inline-block rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    + Nuevo Cliente
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b">
                                <th class="py-2">ID</th>
                                <th>Nombre</th>
                                <th>DNI/RUC</th>
                                <th>Teléfono</th>
                                <th class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($clientes as $c)
                                <tr class="border-b">
                                    <td class="py-2">{{ $c->id }}</td>
                                    <td>{{ $c->nombre }}</td>
                                    <td>{{ $c->dni_ruc ?? '-' }}</td>
                                    <td>{{ $c->telefono ?? '-' }}</td>
                                    <td class="text-right space-x-2">
                                        <a class="text-indigo-600 hover:underline" href="{{ route('clientes.show', $c) }}">Ver</a>
                                        <a class="text-yellow-600 hover:underline" href="{{ route('clientes.edit', $c) }}">Editar</a>

                                        <form action="{{ route('clientes.destroy', $c) }}" method="POST" class="inline"
                                              onsubmit="return confirm('¿Eliminar cliente?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 hover:underline" type="submit">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-center text-gray-500">
                                        No hay clientes registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $clientes->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
