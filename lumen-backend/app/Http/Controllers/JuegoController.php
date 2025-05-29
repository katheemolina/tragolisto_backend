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
}
