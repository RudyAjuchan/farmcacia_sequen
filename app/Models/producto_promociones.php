<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class producto_promociones extends Model
{
    use HasFactory;

    public function loteProductos()
    {
        return $this->belongsTo(Lote_producto::class, 'lote_productos_id');
    }

    public function promocion()
    {
        return $this->belongsTo(Promociones::class, 'promociones_id');
    }
}
