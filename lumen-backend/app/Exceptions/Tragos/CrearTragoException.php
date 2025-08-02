<?php
namespace App\Exceptions\Tragos;

use Exception;

class CrearTragoException extends Exception
{
    protected $codeError = 1004;
    public function __construct($message = 'No se pudo crear el trago.')
    {
        parent::__construct($message);
    }
    public function getCodeError() { return $this->codeError; }
} 