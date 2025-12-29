<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'nombre',
        'categoria',
        'precio',
        'stock',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'stock'  => 'integer',
    ];

    // ====== POO: Reglas de negocio ======

    public function tieneStock(int $cantidad = 1): bool
    {
        return $cantidad > 0 && $this->stock >= $cantidad;
    }

    public function descontarStock(int $cantidad): void
    {
        if ($cantidad <= 0) {
            throw new \InvalidArgumentException('La cantidad a descontar debe ser mayor a 0.');
        }

        if (! $this->tieneStock($cantidad)) {
            throw new \DomainException("Stock insuficiente para '{$this->nombre}'. Stock actual: {$this->stock}.");
        }

        $this->stock -= $cantidad;
        $this->save();
    }

    public function aumentarStock(int $cantidad): void
    {
        if ($cantidad <= 0) {
            throw new \InvalidArgumentException('La cantidad a aumentar debe ser mayor a 0.');
        }

        $this->stock += $cantidad;
        $this->save();
    }
}
