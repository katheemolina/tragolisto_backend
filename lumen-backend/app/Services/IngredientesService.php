<?php
namespace App\Services;

use App\Models\Ingrediente;
use App\Exceptions\Ingredientes\IngredientesNoDisponiblesException;
use App\Exceptions\Ingredientes\IngredienteNoEncontradoException;

class IngredientesService
{
    public function obtenerTodosLosIngredientes()
    {
        $ingredientes = Ingrediente::all();
        
        if ($ingredientes->isEmpty()) {
            throw new IngredientesNoDisponiblesException();
        }

        return $ingredientes;
    }

    public function obtenerIngredientePorId($id)
    {
        $ingrediente = Ingrediente::find($id);

        if (!$ingrediente) {
            throw new IngredienteNoEncontradoException();
        }

        return $ingrediente;
    }

    public function crearIngrediente($data)
    {
        try {
            $ingrediente = Ingrediente::create($data);
            return $ingrediente;
        } catch (\Illuminate\Database\QueryException $e) {
            throw new \App\Exceptions\Ingredientes\CrearIngredienteException($e->getMessage());
        } catch (\Exception $e) {
            throw new \App\Exceptions\Ingredientes\CrearIngredienteException('Error al crear el ingrediente.');
        }
    }

    public function actualizarIngrediente($id, $data)
    {
        $ingrediente = Ingrediente::find($id);
        if (!$ingrediente) {
            throw new IngredienteNoEncontradoException();
        }
        try {
            $ingrediente->fill($data);
            $ingrediente->save();
            return $ingrediente;
        } catch (\Exception $e) {
            throw new \App\Exceptions\Ingredientes\ActualizarIngredienteException('No se pudo actualizar el ingrediente.');
        }
    }

    public function eliminarIngrediente($id)
    {
        $ingrediente = Ingrediente::find($id);
        if (!$ingrediente) {
            throw new IngredienteNoEncontradoException();
        }
        try {
            $ingrediente->delete();
            return true;
        } catch (\Exception $e) {
            throw new \App\Exceptions\Ingredientes\EliminarIngredienteException('No se pudo eliminar el ingrediente.');
        }
    }
} 