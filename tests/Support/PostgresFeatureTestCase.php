<?php

declare(strict_types=1);

namespace Tests\Support;

use PHPUnit\Framework\TestCase;

/**
 * Base test case for HTTP and database integration tests against PostgreSQL.
 */
abstract class PostgresFeatureTestCase extends TestCase
{
    protected string $basePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->basePath = dirname(__DIR__, 2);
        PostgresTestEnvironment::syncEnvironmentVariables();
        PostgresTestEnvironment::wipePublicSchema($this->basePath);
    }
}
