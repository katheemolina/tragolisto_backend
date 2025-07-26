<?php
namespace App\Http\Controllers;

use App\Services\TragosService;
use Illuminate\Http\Request;
use App\Exceptions\Tragos\TragosVaciosException;
use App\Exceptions\Tragos\TragosPorIngredientesException;
use App\Exceptions\Tragos\TragoNoEncontradoException;


class TragosController extends Controller
{
    protected $tragosService;

    public function __construct(TragosService $tragosService)
    {
        $this->tragosService = $tragosService;
    }

    // GET /api/tragos
   public function getTragos(Request $request)
{
    try {
        $ingredientes = $request->query('ingredientes');

        if ($ingredientes) {
            $ingredientesArray = explode(',', $ingredientes);
            $tragos = $this->tragosService->getTragosPorIngredientes($ingredientesArray);
            return response()->json(['tragos' => $tragos], 200);
        } else {
            $resultado = $this->tragosService->getAllTragos();
            return response()->json($resultado, 200);
        }
    } catch (TragosVaciosException | TragosPorIngredientesException $e) {
        return response()->json([
        'error' => [
            'code' => $e->getCodeError(),
            'message' => $e->getMessage(),
        ]
    ], 404);
    } catch (\Exception $e) {
        return response()->json([
            'error' => [
                'code' => 5000,
                'message' => 'Error interno del servidor' ,
                $e
            ]
        ], 500);
    }
}

public function getTragoPorID($id)
{
    try {
        $trago = $this->tragosService->getTragoPorID($id);
        return response()->json($trago, 200);
    } catch (TragoNoEncontradoException $e) {
        return response()->json([
        'error' => [
            'code' => $e->getCodeError(),
            'message' => $e->getMessage(),
        ]
    ], 404);
} catch (\Exception $e) {
    return response()->json([
        'error' => [
            'code' => 5000,
            'message' => 'Error interno del servidor'
        ]
    ], 500);
}
}

public function crearTrago(Request $request)
{
    try {
        $data = $request->only([
            'nombre',
            'descripcion',
            'instrucciones',
            'tips',
            'historia',
            'es_alcoholico',
            'imagen_url',
            'dificultad',
            'tiempo_preparacion_minutos'
        ]);
        $this->tragosService->crearTrago($data);
        return response()->json([
            'message' => 'El trago fue agregado correctamente.'
        ], 201);
    } catch (\App\Exceptions\Tragos\CrearTragoException $e) {
        return response()->json([
            'error' => [
                'code' => $e->getCodeError(),
                'message' => $e->getMessage(),
            ]
        ], 400);
    } catch (\Exception $e) {
        return response()->json([
            'error' => [
                'code' => 5000,
                'message' => 'Error interno del servidor',
            ]
        ], 500);
    }
}

public function actualizarTrago(Request $request, $id)
{
    try {
        $data = $request->only([
            'nombre',
            'descripcion',
            'instrucciones',
            'tips',
            'historia',
            'es_alcoholico',
            'imagen_url',
            'dificultad',
            'tiempo_preparacion_minutos'
        ]);
        $this->tragosService->actualizarTrago($id, array_filter($data, function($v) { return !is_null($v); }));
        return response()->json([
            'message' => 'El trago fue actualizado correctamente.'
        ], 200);
    } catch (\App\Exceptions\Tragos\TragoNoEncontradoException $e) {
        return response()->json([
            'error' => [
                'code' => $e->getCodeError(),
                'message' => $e->getMessage(),
            ]
        ], 404);
    } catch (\App\Exceptions\Tragos\ActualizarTragoException $e) {
        return response()->json([
            'error' => [
                'code' => $e->getCodeError(),
                'message' => $e->getMessage(),
            ]
        ], 400);
    } catch (\Exception $e) {
        return response()->json([
            'error' => [
                'code' => 5000,
                'message' => 'Error interno del servidor',
            ]
        ], 500);
    }
}

public function eliminarTrago($id)
{
    try {
        $this->tragosService->eliminarTrago($id);
        return response()->json([
            'message' => 'El trago fue eliminado correctamente.'
        ], 200);
    } catch (\App\Exceptions\Tragos\TragoNoEncontradoException $e) {
        return response()->json([
            'error' => [
                'code' => $e->getCodeError(),
                'message' => $e->getMessage(),
            ]
        ], 404);
    } catch (\App\Exceptions\Tragos\EliminarTragoException $e) {
        return response()->json([
            'error' => [
                'code' => $e->getCodeError(),
                'message' => $e->getMessage(),
            ]
        ], 400);
    } catch (\Exception $e) {
        return response()->json([
            'error' => [
                'code' => 5000,
                'message' => 'Error interno del servidor',
            ]
        ], 500);
    }
}

public function obtenerTop3Favoritos()
{
    try {
        $topTragos = $this->tragosService->obtenerTop5TragosFavoritos();
        return response()->json($topTragos, 200);
    } catch (\App\Exceptions\Tragos\TragosVaciosException $e) {
        return response()->json([
            'error' => [
                'code' => $e->getCodeError(),
                'message' => $e->getMessage(),
            ]
        ], 404);
    } catch (\Exception $e) {
        return response()->json([
            'error' => [
                'code' => 5000,
                'message' => 'Error interno del servidor',
            ]
        ], 500);
    }
}
}
