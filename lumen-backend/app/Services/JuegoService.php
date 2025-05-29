<?php
namespace App\Services;

use App\Models\Juego;
use App\Exceptions\Juegos\JuegosNoDisponiblesException;
use App\Exceptions\Juegos\JuegoNoEncontradoException;

class JuegoService
{
    public function obtenerTodosLosJuegos()
    {
        $juegos = Juego::all();

        if ($juegos->isEmpty()) {
            throw new JuegosNoDisponiblesException();
        }

        return $juegos;
    }

    public function obtenerJuegoPorId($id)
    {
        $juego = Juego::find($id);

        if (!$juego) {
            throw new JuegoNoEncontradoException();
        }

        return $juego;
    }
}
