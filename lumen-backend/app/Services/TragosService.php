<?php
namespace App\Services;

use App\Models\Trago;
use App\Models\Favorito;
use App\Exceptions\Tragos\TragosVaciosException;
use App\Exceptions\Tragos\TragosPorIngredientesException;
use App\Exceptions\Tragos\TragoNoEncontradoException;


class TragosService
{
    public function getAllTragos(bool $esMenor = false)
    {
        $query = Trago::query();

        if ($esMenor) {
            $query->where('es_alcoholico', false);
        }

        $tragos = $query->get();

        if ($tragos->isEmpty()) {
            throw new TragosVaciosException();
        }

        return ['tragos' => $tragos];
    }

    public function getTragosPorIngredientes(array $ingredientes, bool $esMenor = false)
    {
        $query = Trago::whereHas('ingredientes', function ($q) use ($ingredientes) {
            $q->whereIn('nombre', $ingredientes);
        });

        if ($esMenor) {
            $query->where('es_alcoholico', false);
        }

        $tragos = $query->get();

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

public function obtenerTop5TragosFavoritos()
{
    $topTragos = Favorito::select('trago_id')
        ->selectRaw('COUNT(*) as total_favoritos')
        ->with('trago:id,nombre,descripcion')
        ->groupBy('trago_id')
        ->orderBy('total_favoritos', 'desc')
        ->limit(5)
        ->get()
        ->map(function ($favorito) {
            return [
                'trago_id' => $favorito->trago_id,
                'nombre' => $favorito->trago->nombre,
                'descripcion' => $favorito->trago->descripcion,
                'total_favoritos' => $favorito->total_favoritos
            ];
        });

    if ($topTragos->isEmpty()) {
        throw new \App\Exceptions\Tragos\TragosVaciosException();
    }

    return $topTragos;
}

}
