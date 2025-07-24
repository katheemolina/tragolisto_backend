<?php
namespace App\Exceptions\Ingredientes;

use Exception;

class IngredienteNoEncontradoException extends Exception
{
    protected $codeError = 2002;
    protected $message = 'No se encontró el ingrediente seleccionado.';
    public function getCodeError() { return $this->codeError; }
} 