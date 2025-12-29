<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nuevo Cliente
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @include('partials.flash')

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('clientes.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block mb-1">Nombre</label>
                        <input name="nombre" class="w-full rounded border-gray-300"
                               value="{{ old('nombre') }}" required>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1">DNI(8) o RUC(11)</label>
                        <input name="dni_ruc" class="w-full rounded border-gray-300"
                               value="{{ old('dni_ruc') }}">
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1">Teléfono (opcional)</label>
                        <input name="telefono" class="w-full rounded border-gray-300"
                               value="{{ old('telefono') }}">
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('clientes.index') }}" class="rounded bg-gray-200 px-4 py-2">
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
