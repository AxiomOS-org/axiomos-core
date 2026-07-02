<?php

declare(strict_types=1);

namespace App\Platform\Activity;

enum ActivityType: string
{
    case Generic = 'generic';
    case Created = 'created';
    case Updated = 'updated';
    case Deleted = 'deleted';
}
