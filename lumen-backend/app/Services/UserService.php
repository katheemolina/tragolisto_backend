<?php
namespace App\Services;

use App\Models\User;
use App\Exceptions\Usuarios\UsuariosNoDisponiblesException;

class UserService
{
    public function obtenerTodosLosUsuarios()
    {
        $usuarios = User::select('nombre', 'email', 'fecha_nacimiento', 'created_at')
            ->get()
            ->map(function ($usuario) {
                $edad = null;
                if ($usuario->fecha_nacimiento) {
                    $fechaNacimiento = \Carbon\Carbon::parse($usuario->fecha_nacimiento);
                    $edad = $fechaNacimiento->age;
                }
                
                return [
                    'nombre' => $usuario->nombre,
                    'email' => $usuario->email,
                    'fecha_nacimiento' => $usuario->fecha_nacimiento,
                    'edad' => $edad,
                    'fecha_creacion' => $usuario->created_at->toDateString()
                ];
            });
        
        if ($usuarios->isEmpty()) {
            throw new UsuariosNoDisponiblesException();
        }

        return $usuarios;
    }
}
