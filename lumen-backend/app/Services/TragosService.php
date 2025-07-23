<?php
namespace App\Services;

use App\Models\Trago;
use App\Exceptions\Tragos\TragosVaciosException;
use App\Exceptions\Tragos\TragosPorIngredientesException;
use App\Exceptions\Tragos\TragoNoEncontradoException;


class TragosService
{
    public function getAllTragos()
{
    $tragos = Trago::all();
    
    if ($tragos->isEmpty()) {
        throw new TragosVaciosException();
    }

    return [
        'total' => $tragos->count(),
        'tragos' => $tragos
    ];
}

public function getTragosPorIngredientes(array $ingredientes)
{
    $tragos = Trago::whereHas('ingredientes', function ($query) use ($ingredientes) {
        $query->whereIn('nombre', $ingredientes);
    }, '=', count($ingredientes))->get();

    if ($tragos->isEmpty()) {
        throw new TragosPorIngredientesException();
    }

    return $tragos;
}

public function getTragoPorID(int $id)
{
    $trago = Trago::with('ingredientes')->find($id);

    if (!$trago) {
        throw new TragoNoEncontradoException();
    }

    return $trago;
}

public function crearTrago($data)
{
    try {
        $trago = Trago::create($data);
        return $trago;
    } catch (\Illuminate\Database\QueryException $e) {
        throw new \App\Exceptions\Tragos\CrearTragoException($e->getMessage());
    } catch (\Exception $e) {
        throw new \App\Exceptions\Tragos\CrearTragoException('Error al crear el trago.');
    }
}

public function actualizarTrago($id, $data)
{
    $trago = Trago::find($id);
    if (!$trago) {
        throw new \App\Exceptions\Tragos\TragoNoEncontradoException();
    }
    try {
        $trago->fill($data);
        $trago->save();
        return $trago;
    } catch (\Exception $e) {
        throw new \App\Exceptions\Tragos\ActualizarTragoException('No se pudo actualizar el trago.');
    }
}

public function eliminarTrago($id)
{
    $trago = Trago::find($id);
    if (!$trago) {
        throw new \App\Exceptions\Tragos\TragoNoEncontradoException();
    }
    try {
        $trago->delete();
        return true;
    } catch (\Exception $e) {
        throw new \App\Exceptions\Tragos\EliminarTragoException('No se pudo eliminar el trago.');
    }
}

}
