<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Productos extends Model
{
    use HasFactory;
    protected $table = 'productos';
    protected $primaryKey = 'id_producto';
    protected $fillable = [
        'nombre_producto',
        'codigo_barras',
        'categoria',
        'precio_unitario',
        'precio_venta',
        'stock',
        'estado'
    ];
}
