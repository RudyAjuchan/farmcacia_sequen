<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lote_producto extends Model
{
    use HasFactory;

    public function productos()
    {
        return $this->belongsTo(Producto::class, 'productos_id');
    }

    public function detalleVentas()
    {
        return $this->hasMany(Detalle_venta::class);
    }

    public function producto_promocion()
    {
        return $this->hasMany(producto_promociones::class, 'lote_productos_id');
    }
}
