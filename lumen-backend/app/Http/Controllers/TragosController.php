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
                'message' => 'Error interno del servidor'
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

}
