<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JuegoService;
use App\Exceptions\Juegos\JuegosNoDisponiblesException;
use App\Exceptions\Juegos\JuegoNoEncontradoException;

class JuegoController extends Controller
{
    protected $juegoService;

    public function __construct()
    {
        $this->juegoService = new JuegoService();
    }

    public function modoFiesta()
    {
        try {
            $juegos = $this->juegoService->obtenerTodosLosJuegos();
            return response()->json($juegos, 200);
        } catch (JuegosNoDisponiblesException $e) {
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

    public function getJuegoPorID($id)
    {
        try {
            $juego = $this->juegoService->obtenerJuegoPorId($id);
            return response()->json($juego, 200);
        } catch (JuegoNoEncontradoException $e) {
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

    public function crearJuego(Request $request)
    {
        try {
            $data = $request->only([
                'nombre',
                'descripcion',
                'categoria',
                'materiales',
                'min_jugadores',
                'max_jugadores',
                'es_para_beber',
            ]);
            $this->juegoService->crearJuego($data);
            return response()->json([
                'message' => 'El juego fue agregado correctamente.'
            ], 201);
        } catch (\App\Exceptions\Juegos\CrearJuegoException $e) {
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

    public function actualizarJuego(Request $request, $id)
    {
        try {
            $data = $request->only([
                'nombre',
                'descripcion',
                'categoria',
                'materiales',
                'min_jugadores',
                'max_jugadores',
                'es_para_beber',
            ]);
            $this->juegoService->actualizarJuego($id, array_filter($data, function($v) { return !is_null($v); }));
            return response()->json([
                'message' => 'El juego fue actualizado correctamente.'
            ], 200);
        } catch (\App\Exceptions\Juegos\JuegoNoEncontradoException $e) {
            return response()->json([
                'error' => [
                    'code' => $e->getCodeError(),
                    'message' => $e->getMessage(),
                ]
            ], 404);
        } catch (\App\Exceptions\Juegos\ActualizarJuegoException $e) {
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

    public function eliminarJuego($id)
    {
        try {
            $this->juegoService->eliminarJuego($id);
            return response()->json([
                'message' => 'El juego fue eliminado correctamente.'
            ], 200);
        } catch (\App\Exceptions\Juegos\JuegoNoEncontradoException $e) {
            return response()->json([
                'error' => [
                    'code' => $e->getCodeError(),
                    'message' => $e->getMessage(),
                ]
            ], 404);
        } catch (\App\Exceptions\Juegos\EliminarJuegoException $e) {
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
}
