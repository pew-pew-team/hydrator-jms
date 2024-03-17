<?php

declare(strict_types=1);

namespace PewPew\Hydrator\JMS\Exception;

use PewPew\Hydrator\Exception\HydratorExceptionInterface;

class HydratorException extends \LogicException implements HydratorExceptionInterface
{
    final public function __construct(string $message, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
