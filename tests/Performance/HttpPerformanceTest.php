<?php

declare(strict_types=1);

namespace Tests\Performance;

use Tests\Support\QA\PerformanceProbe;

final class HttpPerformanceTest extends PerformanceProbe
{
    public function test_health_endpoint_responds_within_threshold(): void
    {
        $this->assertHealthRequestIsFast();
    }

    public function test_organization_list_memory_delta_is_bounded(): void
    {
        $this->assertMemoryDeltaIsBounded();
    }
}
