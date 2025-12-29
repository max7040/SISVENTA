<x-app-layout>
    @php
        $estado = $venta->estado ?? 'abierta';

        $correlativo = ($venta->serie && $venta->numero)
            ? strtoupper($venta->serie) . '-' . str_pad((int)$venta->numero, 6, '0', STR_PAD_LEFT)
            : 'PENDIENTE';
    @endphp

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Venta #{{ $venta->id }} — {{ $correlativo }}
            </h2>

            <a href="{{ route('ventas.index') }}"
               class="rounded bg-gray-200 px-4 py-2 inline-block">
                Volver a ventas
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('partials.flash')

            {{-- Aviso si está abierta (no aparece en historial hasta finalizar) --}}
            @if($estado === 'abierta')
                <div class="mb-4 rounded bg-yellow-50 border border-yellow-200 p-4 text-sm text-yellow-800">
                    Esta venta está <b>ABIERTA</b>. Solo aparecerá en el historial cuando la finalices.
                </div>
            @endif

            {{-- Resumen --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <p><b>Tipo:</b> {{ ucfirst($venta->tipo_comprobante) }}</p>
                    <p><b>Estado:</b> {{ ucfirst($estado) }}</p>

                    <p>
                        <b>Documento:</b> {{ $venta->cliente_documento ?? '-' }}
                        <span class="text-sm text-gray-500">
                            @if($venta->tipo_comprobante === 'factura')
                                (RUC)
                            @else
                                (DNI)
                            @endif
                        </span>
                    </p>

                    <p><b>Fecha:</b> {{ $venta->fecha?->format('Y-m-d H:i') ?? '-' }}</p>
                </div>

                <div class="mt-3 text-right font-semibold">
                    TOTAL: S/ {{ number_format((float)$venta->total, 2) }}
                </div>

                {{-- PDF solo si está cerrada --}}
                @if($estado === 'cerrada')
                    <div class="mt-4">
                        <a href="{{ route('ventas.pdf', $venta) }}"
                           class="inline-block rounded bg-emerald-600 px-4 py-2 text-white hover:bg-emerald-700">
                            Descargar PDF
                        </a>
                    </div>
                @endif
            </div>

            {{-- Si está cerrada, no se puede modificar --}}
            @if($estado === 'cerrada')
                <div class="mb-6 text-sm text-gray-600">
                    Venta finalizada: no se pueden agregar productos ni modificar el detalle.
                </div>
            @endif

            {{-- AGREGAR PRODUCTO (solo si está abierta) --}}
            @if($estado === 'abierta')
                <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
                    <h3 class="font-semibold mb-3">Agregar producto al detalle</h3>

                    <form method="POST" action="{{ route('ventas.items.add', $venta) }}"
                          class="flex flex-col sm:flex-row gap-3">
                        @csrf

                        <select name="producto_id" class="rounded border-gray-300 flex-1" required>
                            <option value="">-- Selecciona producto --</option>
                            @foreach ($productos as $p)
                                <option value="{{ $p->id }}" @selected(old('producto_id') == $p->id) @disabled($p->stock <= 0)>
                                    {{ $p->nombre }}
                                    (Stock: {{ $p->stock }}) - S/ {{ number_format((float)$p->precio, 2) }}
                                    {{ $p->stock <= 0 ? ' (SIN STOCK)' : '' }}
                                </option>
                            @endforeach
                        </select>

                        <input type="number" name="cantidad" min="1" value="{{ old('cantidad', 1) }}"
                               class="rounded border-gray-300 w-32" required>

                        <button class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                            Agregar
                        </button>
                    </form>

                    <p class="text-sm text-gray-600 mt-2">
                        Si agregas el mismo producto, se sumará la cantidad (cuando el método del modelo esté en modo “sumar”).
                    </p>
                </div>
            @endif

            {{-- DETALLE --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-3">Detalle de venta</h3>

                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2">Producto</th>
                            <th>Cant.</th>
                            <th>P. Unit.</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($venta->detalles as $d)
                            <tr class="border-b">
                                <td class="py-2">{{ $d->producto->nombre }}</td>
                                <td>{{ $d->cantidad }}</td>
                                <td>S/ {{ number_format((float)$d->precio_unitario, 2) }}</td>
                                <td>S/ {{ number_format((float)$d->subtotal, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-gray-500">
                                    Aún no hay productos en el detalle.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4 text-right font-semibold">
                    TOTAL: S/ {{ number_format((float)$venta->total, 2) }}
                </div>
            </div>

            {{-- FINALIZAR (abajo del detalle, solo si está abierta) --}}
            @if($estado === 'abierta')
                <div class="bg-white shadow-sm sm:rounded-lg p-6 mt-6">
                    <h3 class="font-semibold mb-3">Finalizar venta</h3>

                    <form method="POST" action="{{ route('ventas.finalizar', $venta) }}"
                          class="flex flex-col sm:flex-row gap-3 items-end">
                        @csrf

                        <div class="flex-1">
                            <label class="block mb-1">
                                @if($venta->tipo_comprobante === 'factura')
                                    RUC (obligatorio, 11 dígitos)
                                @else
                                    DNI (opcional, 8 dígitos)
                                @endif
                            </label>

                            <input name="cliente_documento"
                                   class="w-full rounded border-gray-300"
                                   value="{{ old('cliente_documento') }}"
                                   placeholder="{{ $venta->tipo_comprobante === 'factura' ? '11 dígitos' : '8 dígitos o vacío' }}">
                        </div>

                        <button class="rounded bg-emerald-600 px-4 py-2 text-white hover:bg-emerald-700">
                            Finalizar
                        </button>
                    </form>

                    <p class="text-sm text-gray-600 mt-2">
                        Al finalizar se genera serie/número/fecha y recién se habilita el PDF.
                    </p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
