<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

final class UnitSmokeTest extends TestCase
{
    public function test_unit_suite_bootstraps(): void
    {
        self::assertTrue(true);
    }
}