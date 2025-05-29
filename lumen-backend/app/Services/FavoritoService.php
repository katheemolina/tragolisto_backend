<?php

namespace App\Services;

use App\Models\Favorito;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Exceptions\Favoritos\FavoritosNoEncontradosException;
use App\Exceptions\Favoritos\UsuarioNoEncontradoException;
use App\Exceptions\Favoritos\AgregarFavoritoException;
use App\Exceptions\Favoritos\EliminarFavoritoException;


class FavoritoService
{
    public function guardarFavorito($user_id, $trago_id)
{
    
    // Evitar duplicados (por la UNIQUE)
    $exists = Favorito::where('user_id', $user_id)
                      ->where('trago_id', $trago_id)
                      ->exists();

    if ($exists) {
        throw new AgregarFavoritoException('Este trago ya está en favoritos.');
    }

    try {
        Favorito::create([
            'user_id' => $user_id,
            'trago_id' => $trago_id,
        ]);
        return ['status' => true, 'message' => 'Guardado correctamente'];
    } catch (\Exception $e) {
        throw new AgregarFavoritoException();
    }
}


    public function eliminarFavorito($favorito_id)
{
    try {
        $favorito = Favorito::findOrFail($favorito_id);
        $favorito->delete();
        return ['status' => true, 'message' => 'Eliminado correctamente'];
    } catch (ModelNotFoundException $e) {
        throw new EliminarFavoritoException('No se encontró el favorito con ese ID.');
    } catch (\Exception $e) {
        throw new EliminarFavoritoException();
    }
}


    public function listarFavoritos($user_id)
{
    $favoritos = Favorito::where('user_id', $user_id)
        ->with('trago')
        ->get();

    if ($favoritos->isEmpty()) {
        throw new FavoritosNoEncontradosException();
    }

    return $favoritos;
}

}
