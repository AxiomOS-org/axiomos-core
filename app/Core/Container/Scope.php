<?php

declare(strict_types=1);

namespace App\Core\Container;

/**
 * Binding lifetime scopes for the service container.
 */
enum Scope: string
{
    case Transient = 'transient';
    case Singleton = 'singleton';
    case Scoped = 'scoped';
}
