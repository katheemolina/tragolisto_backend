<?php
namespace App\Exceptions\Ingredientes;

use Exception;

class ActualizarIngredienteException extends Exception
{
    protected $codeError = 2004;
    public function __construct($message = 'No se pudo actualizar el ingrediente.')
    {
        parent::__construct($message);
    }
    public function getCodeError() { return $this->codeError; }
} 