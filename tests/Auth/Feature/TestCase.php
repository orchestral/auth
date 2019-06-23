<?php

namespace Orchestra\Auth\TestCase\Feature;

use Orchestra\Testbench\TestCase as Testbench;

abstract class TestCase extends Testbench
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->withFactories(__DIR__.'/../../factories');
    }

    /**
     * Override application aliases.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function overrideApplicationProviders($app): array
    {
        return [
            'Illuminate\Auth\AuthServiceProvider' => 'Orchestra\Auth\AuthServiceProvider',
        ];
    }
}
