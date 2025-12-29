<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Comprobante</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px; }
        .box { border: 1px solid #333; padding: 10px; }
        .title { font-size: 14px; font-weight: bold; margin: 0 0 6px 0; }
        .small { font-size: 11px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 6px; }
        th { background: #f2f2f2; text-align: left; }
        .right { text-align: right; }
        .mt { margin-top: 12px; }
    </style>
</head>
<body>

@php
    $correlativo = strtoupper($venta->serie) . '-' . str_pad((int)$venta->numero, 6, '0', STR_PAD_LEFT);
    $tipo = strtolower((string)$venta->tipo_comprobante);
    $labelDoc = $tipo === 'factura' ? 'RUC' : 'DNI';
    $doc = $venta->cliente_documento ?? '-';
@endphp

<div class="header">
    <div class="box" style="width: 62%;">
        <p class="title">{{ $empresa['nombre'] }}</p>
        <p class="small"><b>Dirección:</b> {{ $empresa['direccion'] }}</p>
        <p class="small"><b>RUC (simulado):</b> {{ $empresa['ruc'] }}</p>
        <p class="small"><b>Sistema:</b> SISVENTA (comprobante simulado - no SUNAT)</p>
    </div>

    <div class="box" style="width: 34%; text-align:center;">
        <p class="title">{{ strtoupper($venta->tipo_comprobante) }}</p>
        <p style="font-size: 13px; font-weight: bold; margin: 0;">{{ $correlativo }}</p>
        <p class="small" style="margin-top: 6px;"><b>Fecha:</b> {{ $venta->fecha?->format('Y-m-d H:i') }}</p>
    </div>
</div>

<div class="box">
    <p style="margin:0 0 6px 0;"><b>Cliente:</b> {{ $venta->cliente?->nombre ?? 'Sin cliente' }}</p>
    <p style="margin:0 0 6px 0;"><b>{{ $labelDoc }}:</b> {{ $doc }}</p>
    <p style="margin:0;"><b>Teléfono:</b> {{ $venta->cliente?->telefono ?? '-' }}</p>
</div>

<table>
    <thead>
        <tr>
            <th>Producto</th>
            <th class="right">Cant.</th>
            <th class="right">P. Unit.</th>
            <th class="right">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($venta->detalles as $d)
            <tr>
                <td>{{ $d->producto->nombre }}</td>
                <td class="right">{{ $d->cantidad }}</td>
                <td class="right">S/ {{ number_format((float)$d->precio_unitario, 2) }}</td>
                <td class="right">S/ {{ number_format((float)$d->subtotal, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="mt">
    <div class="box" style="width: 45%; margin-left: auto;">
        <p style="margin:0;" class="right"><b>TOTAL:</b> S/ {{ number_format((float)$venta->total, 2) }}</p>
        <p style="margin:6px 0 0 0;" class="small right">* Facturación simulada (no SUNAT)</p>
    </div>
</div>

</body>
</html>
