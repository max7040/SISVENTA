<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Nueva Venta
            </h2>

            <a href="{{ route('ventas.index') }}"
               class="rounded bg-gray-200 px-4 py-2 inline-block">
                Volver al historial
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @include('partials.flash')

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('ventas.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block mb-1 font-medium">Tipo de comprobante</label>
                        <select name="tipo_comprobante" class="w-full rounded border-gray-300" required>
                            <option value="boleta" @selected(old('tipo_comprobante')==='boleta')>Boleta</option>
                            <option value="factura" @selected(old('tipo_comprobante')==='factura')>Factura</option>
                        </select>
                        <p class="text-sm text-gray-600 mt-1">
                            En Boleta el DNI es opcional. En Factura el RUC es obligatorio (se pedirá al finalizar).
                        </p>
                    </div>

                    <button class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                        Crear venta
                    </button>
                </form>
            </div>

            <p class="mt-3 text-sm text-gray-600">
                La venta quedará <b>abierta</b> mientras agregas productos. Solo aparecerá en el historial cuando la finalices.
            </p>
        </div>
    </div>
</x-app-layout>
