<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class venta_detalle extends Model
{
    use HasFactory;
    protected $table = "venta_detalle";
    protected $primaryKey = "id_venta";
    public $timestamps = false;
    protected $fillable = [
        'id_grupo_venta',
        'id_producto',
        'cantidad',
        'precio_unitario',
        'subtotal'
    ];
    public function producto()
    {
        return $this->belongsTo(Productos::class, 'id_producto');
    }
}
