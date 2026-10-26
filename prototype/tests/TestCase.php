<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Pages use @vite, and the build output is gitignored. Tests must not
        // depend on a local `npm run build` having happened.
        $this->withoutVite();
    }
}
