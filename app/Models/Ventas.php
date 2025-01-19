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
}
