<?php

declare(strict_types=1);

namespace App\Platform\CustomFields;

use App\Platform\Support\PlatformRecord;
use Illuminate\Database\Eloquent\SoftDeletes;

final class CustomFieldDefinition extends PlatformRecord
{
    use SoftDeletes;

    protected $table = 'universal_custom_field_definitions';

    protected $fillable = [
        'entity_type',
        'field_key',
        'label',
        'field_type',
        'options',
        'is_required',
        'sort_order',
    ];
}

