<?php

namespace Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testDatabaseConnection()
    {
        $this->assertEquals('sqlite', config('database.default'));
    }
}
