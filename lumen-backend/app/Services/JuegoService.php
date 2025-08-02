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

    public function crearJuego($data)
    {
        try {
            $juego = Juego::create($data);
            return $juego;
        } catch (\Illuminate\Database\QueryException $e) {
            throw new \App\Exceptions\Juegos\CrearJuegoException($e->getMessage());
        } catch (\Exception $e) {
            throw new \App\Exceptions\Juegos\CrearJuegoException('Error al crear el juego.');
        }
    }

    public function actualizarJuego($id, $data)
    {
        $juego = Juego::find($id);
        if (!$juego) {
            throw new \App\Exceptions\Juegos\JuegoNoEncontradoException();
        }
        try {
            $juego->fill($data);
            $juego->save();
            return $juego;
        } catch (\Exception $e) {
            throw new \App\Exceptions\Juegos\ActualizarJuegoException('No se pudo actualizar el juego.');
        }
    }

    public function eliminarJuego($id)
    {
        $juego = Juego::find($id);
        if (!$juego) {
            throw new \App\Exceptions\Juegos\JuegoNoEncontradoException();
        }
        try {
            $juego->delete();
            return true;
        } catch (\Exception $e) {
            throw new \App\Exceptions\Juegos\EliminarJuegoException('No se pudo eliminar el juego.');
        }
    }
}
