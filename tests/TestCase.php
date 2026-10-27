<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        // Check for cached configuration before Laravel boots.
        $configCache = dirname(__DIR__)
            . '/bootstrap/cache/config.php';

        if (file_exists($configCache)) {
            throw new RuntimeException(
                'Tests cannot run with cached Laravel configuration. ' .
                'Remove the configuration cache first.'
            );
        }

        // Verify the testing database before Laravel boots.
        $connection = $_SERVER['DB_CONNECTION']
            ?? $_ENV['DB_CONNECTION']
            ?? getenv('DB_CONNECTION');

        $database = $_SERVER['DB_DATABASE']
            ?? $_ENV['DB_DATABASE']
            ?? getenv('DB_DATABASE');

        if ($connection !== 'sqlite' || $database !== ':memory:') {
            throw new RuntimeException(
                'Unsafe test database configuration. ' .
                'Tests must use SQLite with DB_DATABASE=:memory:.'
            );
        }

        parent::setUp();

        $this->withoutVite();
    }
}