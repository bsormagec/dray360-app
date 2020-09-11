<?php

namespace App\Exceptions;

use Exception;

class RipCmsException extends Exception
{
    public function __construct(string $endpoint, string $body, int $status)
    {
        parent::__construct("RipCmsAPI ProfitTools/{$endpoint} failed({$status}) with message: {$body}");
    }
}
