<?php

namespace App\Exceptions;

use Exception;

class BlackflyException extends Exception
{
    public function __construct(string $endpoint, string $body, int $status)
    {
        parent::__construct("Blackfly {$endpoint} failed({$status}) with message: {$body}");
    }
}
