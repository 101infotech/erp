<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Database\Seeders\RolesAndPermissionsSeeder;

abstract class TestCase extends BaseTestCase
{
    /**
     * Seed the database with roles and permissions before each test
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions for each test
        $this->seed(RolesAndPermissionsSeeder::class);
    }
}
