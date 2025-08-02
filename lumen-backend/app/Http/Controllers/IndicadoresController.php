<?php

namespace App\Http\Controllers;

use App\Services\IndicadoresService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class IndicadoresController extends Controller
{
    protected $indicadoresService;

    public function __construct(IndicadoresService $indicadoresService)
    {
        $this->indicadoresService = $indicadoresService;
    }

    /**
     * Obtiene los indicadores de la plataforma
     */
    public function obtenerIndicadores(): JsonResponse
    {
        try {
            $indicadores = $this->indicadoresService->obtenerIndicadores();
            
            return response()->json([
                'success' => true,
                'message' => 'Indicadores obtenidos exitosamente',
                'data' => $indicadores
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los indicadores',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 