<?php

declare(strict_types=1);

namespace App\Core\Container\Exceptions;

final class CircularDependencyException extends ContainerException
{
    /**
     * @param list<string> $stack
     */
    public static function detected(string $abstract, array $stack): self
    {
        return new self(sprintf(
            'Circular dependency detected while resolving "%s": %s',
            $abstract,
            implode(' -> ', [...$stack, $abstract]),
        ));
    }
}
