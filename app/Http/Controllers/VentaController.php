<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        $q = Venta::query()
            ->where('estado', 'cerrada')
            ->whereNotNull('fecha');

        // Filtro rápido: hoy
        if ($request->get('hoy') === '1') {
            $q->whereDate('fecha', now()->toDateString());
        }

        // Filtro por fecha exacta: ?fecha=YYYY-MM-DD
        if ($request->filled('fecha')) {
            $q->whereDate('fecha', $request->input('fecha'));
        }

        $ventas = $q->orderByDesc('fecha')
            ->paginate(10)
            ->withQueryString();

        return view('ventas.index', compact('ventas'));
    }

    public function create()
    {
        return view('ventas.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo_comprobante' => ['required', 'in:boleta,factura'],
        ]);

        $venta = new Venta();
        $venta->tipo_comprobante = $data['tipo_comprobante'];
        $venta->estado = 'abierta';
        $venta->total = 0;
        $venta->save();

        return redirect()->route('ventas.show', $venta)
            ->with('success', 'Venta creada. Agrega productos y luego finaliza.');
    }

    public function show(Venta $venta)
    {
        $venta->load(['detalles.producto']);

        // Si está abierta, sí necesitamos productos para agregar
        $productos = ($venta->estado ?? 'abierta') === 'abierta'
            ? Producto::where('activo', true)->orderBy('nombre')->get()
            : collect();

        return view('ventas.show', compact('venta', 'productos'));
    }

    public function addItem(Request $request, Venta $venta)
    {
        // Si nadie puede ver abiertas, normalmente tampoco deberían modificar.
        // Pero lo dejamos por si tu flujo lo sigue usando.
        $data = $request->validate([
            'producto_id' => ['required', 'exists:productos,id'],
            'cantidad'    => ['required', 'integer', 'min:1'],
        ]);

        try {
            $producto = Producto::findOrFail($data['producto_id']);
            $venta->agregarProducto($producto, (int)$data['cantidad']);

            return back()->with('success', 'Producto agregado/sumado y stock actualizado.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function finalizar(Request $request, Venta $venta)
    {
        $documento = trim((string) $request->input('cliente_documento', ''));
        $documento = $documento === '' ? null : $documento;

        try {
            $venta->finalizar($documento);
            return back()->with('success', 'Venta finalizada. Ya puedes descargar el PDF.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function pdf(Venta $venta)
    {
        if ($venta->estado !== 'cerrada') {
            return back()->with('error', 'Primero finaliza la venta para generar el PDF.');
        }

        $venta->load(['detalles.producto']);

        $pdf = Pdf::loadView('ventas.comprobante', [
            'venta' => $venta,
            'empresa' => [
                'nombre'    => 'Microemprendimiento SISVENTA',
                'direccion' => 'Puno - Perú',
                'ruc'       => '00000000000',
            ],
        ]);

        $file = strtoupper($venta->serie) . '-' . str_pad($venta->numero, 6, '0', STR_PAD_LEFT) . '.pdf';
        return $pdf->download($file);
    }
}
