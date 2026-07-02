<?php

declare(strict_types=1);

namespace App\Core\Kernel;

/**
 * Kernel lifecycle states.
 */
enum KernelState: string
{
    case Cold = 'cold';
    case Initializing = 'initializing';
    case Initialized = 'initialized';
    case Registering = 'registering';
    case Registered = 'registered';
    case Booting = 'booting';
    case Ready = 'ready';
    case ShuttingDown = 'shutting_down';
    case Shutdown = 'shutdown';
}
