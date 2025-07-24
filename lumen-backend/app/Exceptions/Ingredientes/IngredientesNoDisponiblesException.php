<?php
namespace App\Exceptions\Ingredientes;

use Exception;

class IngredientesNoDisponiblesException extends Exception
{
    protected $codeError = 2001;
    protected $message = 'No hay ingredientes disponibles.';
    public function getCodeError() { return $this->codeError; }
} 