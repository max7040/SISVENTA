<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Venta extends Model
{
    protected $table = 'ventas';

    protected $fillable = [
        'cliente_id',
        'estado',
        'cliente_documento',
        'tipo_comprobante',
        'serie',
        'numero',
        'fecha',
        'total',
    ];


    protected $casts = [
        'fecha' => 'datetime',
        'total' => 'decimal:2',
    ];


    // ====== Relaciones ======

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleVenta::class, 'venta_id');
    }

    // ====== POO: Estado ======

    public function estaCerrada(): bool
    {
        return ($this->estado ?? 'abierta') === 'cerrada';
    }

    // ====== POO: Total ======

    public function calcularTotal(): float
    {
        return (float) $this->detalles()->sum('subtotal');
    }

    public function recalcularYGuardarTotal(): void
    {
        $this->total = $this->calcularTotal();
        $this->save();
    }

    /**
     * Agrega un producto a la venta:
     * - Si el producto ya existe en el detalle, SUMA cantidad y recalcula subtotal.
     * - Descuenta stock SOLO por la cantidad agregada.
     * - No permite modificar si la venta está cerrada.
     */
    public function agregarProducto(Producto $producto, int $cantidad): DetalleVenta
    {
        if ($this->estaCerrada()) {
            throw new \DomainException('La venta ya está finalizada. No se puede modificar.');
        }

        if ($cantidad <= 0) {
            throw new \InvalidArgumentException('La cantidad debe ser mayor a 0.');
        }

        return DB::transaction(function () use ($producto, $cantidad) {
            $producto->refresh();

            if (! $producto->tieneStock($cantidad)) {
                throw new \DomainException("Stock insuficiente para '{$producto->nombre}'.");
            }

            // Busca si ya existe el detalle con ese producto
            $detalle = $this->detalles()->where('producto_id', $producto->id)->first();

            $precioUnit = (float) $producto->precio;

            if ($detalle) {
                // ✅ Sumar cantidades
                $detalle->cantidad += $cantidad;
                $detalle->precio_unitario = number_format($precioUnit, 2, '.', '');
                $detalle->subtotal = number_format($detalle->cantidad * $precioUnit, 2, '.', '');
                $detalle->save();
            } else {
                // Crear nuevo detalle
                $detalle = $this->detalles()->create([
                    'producto_id'     => $producto->id,
                    'cantidad'        => $cantidad,
                    'precio_unitario' => number_format($precioUnit, 2, '.', ''),
                    'subtotal'        => number_format($precioUnit * $cantidad, 2, '.', ''),
                ]);
            }

            // Descontar stock SOLO por la cantidad agregada
            $producto->descontarStock($cantidad);

            // Recalcular total
            $this->recalcularYGuardarTotal();

            return $detalle;
        });
    }

    /**
     * Finaliza la venta:
     * - Valida documento: factura => RUC 11 obligatorio; boleta => DNI 8 opcional
     * - Genera serie + correlativo + fecha
     * - Cambia estado a cerrada
     * - Luego recién se permite PDF
     */
    public function finalizar(?string $documento = null): void
    {
        if ($this->estaCerrada()) {
            throw new \DomainException('La venta ya está finalizada.');
        }

        // No finalizar si no hay productos
        if ($this->detalles()->count() === 0) {
            throw new \DomainException('No puedes finalizar una venta sin productos.');
        }

        $tipo = strtolower(trim((string) $this->tipo_comprobante));
        if (!in_array($tipo, ['boleta', 'factura'])) {
            throw new \InvalidArgumentException('Tipo de comprobante inválido.');
        }

        $documento = $documento ? preg_replace('/\D+/', '', $documento) : null;
        // \D+ elimina todo lo que NO sea dígito (espacios, guiones, etc.)

        // Validación documento (sin SUNAT)
        if ($tipo === 'factura') {
            if (! $documento || !preg_match('/^\d{11}$/', $documento)) {
                throw new \DomainException('Para FACTURA el RUC es obligatorio (11 dígitos).');
            }
            $serie = 'F001';
        } else {
            // BOLETA: DNI opcional, si se ingresa debe ser 8 dígitos
            if ($documento && !preg_match('/^\d{8}$/', $documento)) {
                throw new \DomainException('Para BOLETA, el DNI debe tener 8 dígitos o dejarse vacío.');
            }
            $serie = 'B001';
        }

        DB::transaction(function () use ($serie, $documento) {
            // Correlativo por serie
            $ultimo = (int) DB::table('ventas')
                ->where('serie', $serie)
                ->lockForUpdate()
                ->max('numero');

            $this->serie = $serie;
            $this->numero = $ultimo + 1;
            $this->fecha = now();

            $this->cliente_documento = $documento;

            // Total final
            $this->total = number_format($this->calcularTotal(), 2, '.', '');

            $this->estado = 'cerrada';
            $this->save();
        });
    }
}
