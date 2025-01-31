<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ventas extends Model
{
    use HasFactory;

    protected $table = 'ventas';
    protected $primaryKey = 'id_ventas';
    public $timestamps = false;
    protected $fillable = [
        'id_usuario',
        'fecha_venta',
        'total_venta',
    ];
    // Relación con el usuario (vendedor)
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    // Relación con los detalles de la venta
    public function ventaDetalles()
    {
        return $this->hasMany(venta_detalle::class, 'id_grupo_venta');
    }
}
