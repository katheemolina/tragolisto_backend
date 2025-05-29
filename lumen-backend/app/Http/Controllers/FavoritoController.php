<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FavoritoService;
use App\Exceptions\Favoritos\FavoritosNoEncontradosException;
use App\Exceptions\Favoritos\UsuarioNoEncontradoException;
use App\Exceptions\Favoritos\AgregarFavoritoException;
use App\Exceptions\Favoritos\EliminarFavoritoException;

class FavoritoController extends Controller
{
    protected $favoritoService;

    public function __construct(FavoritoService $favoritoService)
    {
        $this->favoritoService = $favoritoService;
    }

   public function guardar(Request $request)
{
    $this->validate($request, [
        'user_id' => 'required|integer',
        'trago_id' => 'required|integer',
    ]);

    try {
        // Debug: ver qué datos llegan al controlador
        \Log::info('Guardar favorito datos:', [
            'user_id' => $request->input('user_id'),
            'trago_id' => $request->input('trago_id')
        ]);

        $result = $this->favoritoService->guardarFavorito(
            $request->input('user_id'),
            $request->input('trago_id')
        );

        \Log::info('Resultado del servicio:', $result);

        if (!$result['status']) {
            return response()->json([
                'error' => ['message' => $result['message']]
            ], 400);
        }

        return response()->json($result, 200);

    } catch (AgregarFavoritoException | UsuarioNoEncontradoException $e) {
        \Log::error('Error específico:', ['message' => $e->getMessage(), 'code' => $e->getCode()]);
        return response()->json([
            'error' => ['code' => $e->getCode(), 'message' => $e->getMessage()]
        ], 400);
    } catch (\Exception $e) {
        \Log::error('Error general:', ['message' => $e->getMessage()]);
        return response()->json([
            'error' => ['code' => 5000, 'message' => 'Error interno del servidor']
        ], 500);
    }
}


    public function eliminar($favorito_id)
    {
        try {
            $result = $this->favoritoService->eliminarFavorito($favorito_id);

            if (!$result['status']) {
                return response()->json([
                    'error' => [
                        'message' => $result['message']
                    ]
                ], 400);
            }

            return response()->json($result, 200);

        } catch (EliminarFavoritoException $e) {
            return response()->json([
                'error' => [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                ]
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'code' => 5000,
                    'message' => 'Error interno del servidor'
                ]
            ], 500);
        }
    }

    public function listar($user_id)
    {
        try {
            $favoritos = $this->favoritoService->listarFavoritos($user_id);
            return response()->json($favoritos, 200);
        } catch (FavoritosNoEncontradosException | UsuarioNoEncontradoException $e) {
            return response()->json([
                'error' => [
                    'code' => $e->getCode(),
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
