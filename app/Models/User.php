<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'id',
        'name',
        'activo',
        'email',
        'password',
        'rol',
    ];
    public static $roles = [
        1 => 'Superadministrador',  // Super administrador
        2 => 'Gerente',             // Gerente
        3 => 'Vendedor'            // Vendedor
    ];

    // Definir permisos según rol
    public static $rolesPermissions = [
        1 => ['*'],                       
        2 => ['ver-reportes', 'vender', 'administrar-productos', 'administrar-ventas'], 
        3 => ['vender'],            
    ];
    protected $hidden = [

        'remember_token',
        'password'
    ];


}
