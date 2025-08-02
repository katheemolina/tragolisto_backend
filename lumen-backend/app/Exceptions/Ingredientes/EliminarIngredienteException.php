<?php
namespace App\Exceptions\Ingredientes;

use Exception;

class EliminarIngredienteException extends Exception
{
    protected $codeError = 2005;
    public function __construct($message = 'No se pudo eliminar el ingrediente.')
    {
        parent::__construct($message);
    }
    public function getCodeError() { return $this->codeError; }
} 