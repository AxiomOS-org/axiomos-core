<?php

declare(strict_types=1);

namespace App\Platform\CustomFields;

use App\Platform\Support\PlatformRecord;

final class CustomFieldValue extends PlatformRecord
{
    protected $table = 'universal_custom_field_values';

    protected $fillable = [
        'definition_id',
        'entity_type',
        'entity_id',
        'value',
    ];
}

