<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    use HasFactory;

    public function proveedor()
    {
        return $this->belongsTo(Proveedores::class);
    }

    public function detalleCompras()
    {
        return $this->hasMany(Detalle_compra::class);
    }
}
