<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Str;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        // Run before parent::setUp() / RefreshDatabase: app() is not always a full Application yet.
        $this->guardAgainstDestructiveTestDatabase();

        parent::setUp();
    }

    /**
     * RefreshDatabase runs migrate:fresh. Refuse MySQL/MariaDB unless the database name is clearly dedicated to tests.
     */
    protected function guardAgainstDestructiveTestDatabase(): void
    {
        if (env('APP_ENV') !== 'testing') {
            return;
        }

        $connection = (string) env('DB_CONNECTION', 'sqlite');
        $database = (string) env('DB_DATABASE', ':memory:');

        if ($connection === 'sqlite') {
            if ($database === ':memory:' || Str::contains($database, 'testing')) {
                return;
            }
        }

        if (in_array($connection, ['mysql', 'mariadb'], true)) {
            if ($database === 'testing' || preg_match('/_(test|testing)$/', $database) === 1) {
                return;
            }
        }

        if (in_array($connection, ['pgsql', 'postgres'], true)) {
            if ($database === 'testing' || preg_match('/_(test|testing)$/', $database) === 1) {
                return;
            }
        }

        $this->fail(
            'Unsafe test database configuration: connection "' . $connection . '" database "' . $database . '". '
            . 'PHPUnit should use sqlite :memory: (see phpunit.xml). '
            . 'If you must use MySQL, use a separate database whose name ends with _test or _testing.'
        );
    }
}
