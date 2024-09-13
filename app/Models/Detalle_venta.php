<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detalle_venta extends Model
{
    use HasFactory;

    public function venta()
    {
        return $this->belongsTo(Ventas::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function loteProducto()
    {
        return $this->belongsTo(Lote_producto::class);
    }
}
