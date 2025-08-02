<?php
namespace App\Exceptions\Tragos;

use Exception;

class EliminarTragoException extends Exception
{
    protected $codeError = 1006;
    public function __construct($message = 'No se pudo eliminar el trago.')
    {
        parent::__construct($message);
    }
    public function getCodeError() { return $this->codeError; }
} 