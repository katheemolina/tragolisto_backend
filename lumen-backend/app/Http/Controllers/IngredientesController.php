<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\IngredientesService;
use App\Exceptions\Ingredientes\IngredientesNoDisponiblesException;
use App\Exceptions\Ingredientes\IngredienteNoEncontradoException;

class IngredientesController extends Controller
{
    protected $ingredientesService;

    public function __construct()
    {
        $this->ingredientesService = new IngredientesService();
    }

    public function obtenerIngredientes()
    {
        try {
            $ingredientes = $this->ingredientesService->obtenerTodosLosIngredientes();
            return response()->json($ingredientes, 200);
        } catch (IngredientesNoDisponiblesException $e) {
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

    public function obtenerIngredientePorId($id)
    {
        try {
            $ingrediente = $this->ingredientesService->obtenerIngredientePorId($id);
            return response()->json($ingrediente, 200);
        } catch (IngredienteNoEncontradoException $e) {
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

    public function crearIngrediente(Request $request)
    {
        try {
            $data = $request->only([
                'nombre',
                'es_alcohol',
                'categoria'
            ]);
            $this->ingredientesService->crearIngrediente($data);
            return response()->json([
                'message' => 'El ingrediente fue agregado correctamente.'
            ], 201);
        } catch (\App\Exceptions\Ingredientes\CrearIngredienteException $e) {
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

    public function actualizarIngrediente(Request $request, $id)
    {
        try {
            $data = $request->only([
                'nombre',
                'es_alcohol',
                'categoria'
            ]);
            $this->ingredientesService->actualizarIngrediente($id, array_filter($data, function($v) { return !is_null($v); }));
            return response()->json([
                'message' => 'El ingrediente fue actualizado correctamente.'
            ], 200);
        } catch (IngredienteNoEncontradoException $e) {
            return response()->json([
                'error' => [
                    'code' => $e->getCodeError(),
                    'message' => $e->getMessage(),
                ]
            ], 404);
        } catch (\App\Exceptions\Ingredientes\ActualizarIngredienteException $e) {
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

    public function eliminarIngrediente($id)
    {
        try {
            $this->ingredientesService->eliminarIngrediente($id);
            return response()->json([
                'message' => 'El ingrediente fue eliminado correctamente.'
            ], 200);
        } catch (IngredienteNoEncontradoException $e) {
            return response()->json([
                'error' => [
                    'code' => $e->getCodeError(),
                    'message' => $e->getMessage(),
                ]
            ], 404);
        } catch (\App\Exceptions\Ingredientes\EliminarIngredienteException $e) {
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