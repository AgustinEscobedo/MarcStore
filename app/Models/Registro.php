<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registro extends Model
{
    use HasFactory;
    protected $table = 'registros';
    protected $primaryKey = 'id_registro';
    protected $fillable = [
        'id_registro',
        'id_usuario',
        'tipo',
        'fecha_hora'
    ];

    // Relación con el usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
