<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Module;

use App\Core\Module\Events\ModuleDisabled;
use App\Core\Module\Events\ModuleEnabled;
use App\Core\Module\Events\ModuleRegistered;
use App\Core\Module\ModuleManifest;
use App\Core\Module\ModuleRegistry;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\TestCase;

/**
 * Feature coverage for the module registry: registration, duplicate protection,
 * lookups, enabled/disabled partitioning, state transitions and events.
 */
final class ModuleRegistryTest extends TestCase
{
    public function test_it_registers_and_finds_modules_by_uuid_and_name(): void
    {
        $registry = new ModuleRegistry();
        $manifest = $this->manifest('Authentication');

        $registry->register($manifest);

        self::assertTrue($registry->has($manifest->uuid));
        self::assertSame($manifest, $registry->findByUuid($manifest->uuid));
        self::assertSame($manifest, $registry->findByName('Authentication'));
    }

    public function test_it_finds_by_uuid_case_insensitively(): void
    {
        $registry = new ModuleRegistry();
        $manifest = $this->manifest('Audit', uuid: 'aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee');

        $registry->register($manifest);

        self::assertNotNull($registry->findByUuid('AAAAAAAA-BBBB-CCCC-DDDD-EEEEEEEEEEEE'));
    }

    public function test_it_returns_null_for_unknown_lookups(): void
    {
        $registry = new ModuleRegistry();

        self::assertNull($registry->findByUuid('00000000-0000-5000-8000-000000000000'));
        self::assertNull($registry->findByName('Nope'));
        self::assertFalse($registry->has('00000000-0000-5000-8000-000000000000'));
    }

    public function test_it_prevents_duplicate_uuid_registration(): void
    {
        $registry = new ModuleRegistry();
        $uuid = 'aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee';
        $registry->register($this->manifest('Alpha', uuid: $uuid));

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('already registered');

        $registry->register($this->manifest('Beta', uuid: $uuid));
    }

    public function test_it_prevents_duplicate_name_registration(): void
    {
        $registry = new ModuleRegistry();
        $registry->register($this->manifest('Authentication', uuid: 'aaaaaaaa-0000-5000-8000-000000000001'));

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('already registered');

        $registry->register($this->manifest('Authentication', uuid: 'aaaaaaaa-0000-5000-8000-000000000002'));
    }

    public function test_it_partitions_enabled_and_disabled_modules(): void
    {
        $registry = new ModuleRegistry();
        $registry->register($this->manifest('On', uuid: $this->uuid(1), enabled: true));
        $registry->register($this->manifest('Off', uuid: $this->uuid(2), enabled: false));

        self::assertInstanceOf(Collection::class, $registry->enabled());
        self::assertSame(['On'], $registry->enabled()->map(static fn (ModuleManifest $m): string => $m->name)->all());
        self::assertSame(['Off'], $registry->disabled()->map(static fn (ModuleManifest $m): string => $m->name)->all());
    }

    public function test_it_enables_a_disabled_module(): void
    {
        $registry = new ModuleRegistry();
        $manifest = $this->manifest('Plugin', enabled: false);
        $registry->register($manifest);

        $updated = $registry->enable($manifest->uuid);

        self::assertTrue($updated->enabled);
        self::assertTrue($registry->findByUuid($manifest->uuid)->enabled);
    }

    public function test_it_disables_an_enabled_module(): void
    {
        $registry = new ModuleRegistry();
        $manifest = $this->manifest('Plugin', enabled: true);
        $registry->register($manifest);

        $updated = $registry->disable($manifest->uuid);

        self::assertFalse($updated->enabled);
        self::assertFalse($registry->findByUuid($manifest->uuid)->enabled);
    }

    public function test_it_removes_a_module(): void
    {
        $registry = new ModuleRegistry();
        $manifest = $this->manifest('Temp');
        $registry->register($manifest);

        $registry->remove($manifest->uuid);

        self::assertFalse($registry->has($manifest->uuid));
        self::assertNull($registry->findByName('Temp'));
    }

    public function test_it_allows_reusing_a_name_after_removal(): void
    {
        $registry = new ModuleRegistry();
        $manifest = $this->manifest('Reusable', uuid: $this->uuid(1));
        $registry->register($manifest);
        $registry->remove($manifest->uuid);

        $registry->register($this->manifest('Reusable', uuid: $this->uuid(2)));

        self::assertTrue($registry->has($this->uuid(2)));
    }

    public function test_it_throws_when_toggling_or_removing_an_unknown_module(): void
    {
        $registry = new ModuleRegistry();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('is not registered');

        $registry->enable('00000000-0000-5000-8000-000000000000');
    }

    public function test_it_fires_registration_and_transition_events(): void
    {
        $events = new Dispatcher();
        $captured = [];
        $events->listen('*', static function (string $event) use (&$captured): void {
            $captured[] = $event;
        });

        $registry = new ModuleRegistry($events);
        $manifest = $this->manifest('Eventful', enabled: false);

        $registry->register($manifest);
        $registry->enable($manifest->uuid);
        $registry->disable($manifest->uuid);

        self::assertContains(ModuleRegistered::class, $captured);
        self::assertContains(ModuleEnabled::class, $captured);
        self::assertContains(ModuleDisabled::class, $captured);
    }

    public function test_state_toggles_are_idempotent_and_do_not_refire(): void
    {
        $events = new Dispatcher();
        $enabledEvents = 0;
        $events->listen(ModuleEnabled::class, static function () use (&$enabledEvents): void {
            $enabledEvents++;
        });

        $registry = new ModuleRegistry($events);
        $manifest = $this->manifest('Steady', enabled: true);
        $registry->register($manifest);

        $registry->enable($manifest->uuid);
        $registry->enable($manifest->uuid);

        self::assertSame(0, $enabledEvents, 'Enabling an already-enabled module must not fire an event.');
    }

    private function manifest(string $name, ?string $uuid = null, bool $enabled = true): ModuleManifest
    {
        return new ModuleManifest(
            uuid: $uuid ?? $this->uuid(crc32($name)),
            id: $name,
            name: $name,
            version: '1.0.0',
            provider: sprintf('Modules\\%s\\Providers\\%sServiceProvider', $name, $name),
            enabled: $enabled,
            priority: 1,
            dependencies: [],
            authors: [['name' => 'AxiomOS Team']],
            path: '/modules/' . $name,
        );
    }

    private function uuid(int $seed): string
    {
        return sprintf('%08x-0000-5000-8000-000000000000', $seed & 0xffffffff);
    }
}
