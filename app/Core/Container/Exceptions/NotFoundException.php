<?php

declare(strict_types=1);

namespace App\Core\Container\Exceptions;

use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

final class NotFoundException extends RuntimeException implements NotFoundExceptionInterface
{
    public static function forAbstract(string $abstract): self
    {
        return new self(sprintf('Service "%s" is not bound.', $abstract));
    }
}
