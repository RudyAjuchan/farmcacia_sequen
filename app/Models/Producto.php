<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    public function subcategoria()
    {
        return $this->belongsTo(Subcategoria::class);
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedores::class);
    }

    public function loteProductos()
    {
        return $this->hasMany(Lote_producto::class);
    }

    public function detalleVentas()
    {
        return $this->hasMany(Detalle_venta::class);
    }

    public function detalleCompras()
    {
        return $this->hasMany(Detalle_compra::class);
    }

    public function promociones()
    {
        return $this->belongsToMany(Promociones::class, 'producto_promocion');
    }
}
