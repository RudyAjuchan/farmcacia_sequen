<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promociones extends Model
{
    use HasFactory;
    
    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'producto_promocion');
    }
}
