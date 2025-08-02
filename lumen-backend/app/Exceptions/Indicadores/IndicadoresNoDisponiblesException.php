<?php

namespace App\Exceptions\Indicadores;

use Exception;

class IndicadoresNoDisponiblesException extends Exception
{
    public function __construct($message = 'No se pudieron obtener los indicadores de la plataforma')
    {
        parent::__construct($message);
    }

    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'error' => 'IndicadoresNoDisponiblesException'
        ], 500);
    }
} 