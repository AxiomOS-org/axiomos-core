<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Kernel;

use App\Core\Kernel\Contracts\KernelInterface;
use App\Core\Kernel\Kernel;
use App\Core\Kernel\KernelManager;
use PHPUnit\Framework\TestCase;

final class KernelManagerContractTest extends TestCase
{
    public function test_kernel_manager_depends_on_kernel_interface(): void
    {
        $kernel = $this->createMock(KernelInterface::class);

        $manager = new KernelManager($kernel);

        self::assertSame($kernel, $manager->kernel());
    }

    public function test_kernel_implements_kernel_interface(): void
    {
        self::assertContains(KernelInterface::class, class_implements(Kernel::class));
    }
}
