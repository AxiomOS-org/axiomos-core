<?php

declare(strict_types=1);

namespace App\Platform\Support;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Base record for platform persistence tables.
 */
abstract class PlatformRecord extends Model
{
    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';
}

