<?php

namespace App\Services;

use App\Models\Trago;
use App\Models\Ingrediente;
use App\Models\Juego;
use App\Models\User;
use App\Exceptions\Indicadores\IndicadoresNoDisponiblesException;
use Carbon\Carbon;

class IndicadoresService
{
    /**
     * Obtiene todos los indicadores de la plataforma
     */
    public function obtenerIndicadores()
    {
        try {
            $indicadores = [
                'total_tragos' => $this->obtenerTotalTragos(),
                'total_ingredientes' => $this->obtenerTotalIngredientes(),
                'total_juegos' => $this->obtenerTotalJuegos(),
                'total_usuarios' => $this->obtenerTotalUsuarios()
            ];

            return $indicadores;
        } catch (\Exception $e) {
            throw new IndicadoresNoDisponiblesException('Error al obtener los indicadores: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene el total de tragos en la plataforma
     */
    public function obtenerTotalTragos()
    {
        return Trago::count();
    }

    /**
     * Obtiene el total de ingredientes en la plataforma
     */
    public function obtenerTotalIngredientes()
    {
        return Ingrediente::count();
    }

    /**
     * Obtiene el total de juegos en la plataforma
     */
    public function obtenerTotalJuegos()
    {
        return Juego::count();
    }

    /**
     * Obtiene el total de usuarios en la plataforma
     */
    public function obtenerTotalUsuarios()
    {
        return User::count();
    }






} 