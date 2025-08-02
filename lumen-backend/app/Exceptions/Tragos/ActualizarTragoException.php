<?php
namespace App\Exceptions\Tragos;

use Exception;

class ActualizarTragoException extends Exception
{
    protected $codeError = 1005;
    public function __construct($message = 'No se pudo actualizar el trago.')
    {
        parent::__construct($message);
    }
    public function getCodeError() { return $this->codeError; }
} 