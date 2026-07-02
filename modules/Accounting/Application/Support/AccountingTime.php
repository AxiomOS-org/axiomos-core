<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\Support;

use Illuminate\Support\Carbon;

final class AccountingTime
{
    public static function now(): Carbon
    {
        return Carbon::now();
    }
}
