<?php

declare(strict_types=1);

namespace App\Core\Container\Contracts;

/**
 * Backward-compatible alias for {@see ContainerInterface}.
 *
 * The kernel and legacy call-sites type-hint this interface; it carries no
 * additional methods.
 */
interface ServiceContainer extends ContainerInterface
{
}
