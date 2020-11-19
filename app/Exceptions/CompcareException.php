<?php

namespace App\Exceptions;

use Exception;

class CompcareException extends Exception
{
    public function __construct(string $endpoint, string $body, int $status)
    {
        parent::__construct("Compcare {$endpoint} failed({$status}) with message: {$body}");
    }
}
