<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class proveedores extends Model
{
    use HasFactory;
    protected $table = 'proveedores';

    // Clave primaria
    protected $primaryKey = 'id_proveedor';

    // Campos asignables masivamente
    protected $fillable = [
        'nombre_proveedor',
        'contacto',
        'telefono',
        'email',
        'estado'
    ];
}
