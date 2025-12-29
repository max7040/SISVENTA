<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $fillable = [
        'nombre',
        'dni_ruc',
        'telefono',
    ];

    // Relación: un cliente tiene muchas ventas
    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class, 'cliente_id');
    }
    protected $casts = [
    'frecuente' => 'boolean',
    'activo' => 'boolean',
    ];

    public function scopeActivos($q)
    {
        return $q->where('activo', true);
    }

    public function scopeFrecuentes($q)
    {
        return $q->where('frecuente', true);
    }

}
