<?php
namespace App\Exceptions\Ingredientes;

use Exception;

class CrearIngredienteException extends Exception
{
    protected $codeError = 2003;
    public function __construct($message = 'No se pudo crear el ingrediente.')
    {
        parent::__construct($message);
    }
    public function getCodeError() { return $this->codeError; }
} 