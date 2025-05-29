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

}
