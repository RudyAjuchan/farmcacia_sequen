<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    public function cliente()
    {
        return $this->belongsTo(User::class, 'clientes_id');
    }

    public function detallePedido()
    {
        return $this->hasMany(Detalle_pedido::class, 'pedidos_id');
    }
}
