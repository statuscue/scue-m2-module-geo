<?php

namespace Scue\Geo\Exceptions;

class OutOfCountry extends \Exception
{
    private $response;

    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null, array $response = [])
    {
        $this->response = $response;
        parent::__construct($message, $code, $previous);
    }

    public function getResponse(): array
    {
        return $this->response;
    }
}