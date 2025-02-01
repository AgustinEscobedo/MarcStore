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
        'id_proveedor',
        'estado'
    ];

    public function proveedor()
    {
        return $this->belongsTo(proveedores::class, 'id_proveedor', 'id_proveedor');
    }
}
