<?php

namespace Orchestra\Auth\Tests\Feature;

use Orchestra\Testbench\TestCase as Testbench;

abstract class TestCase extends Testbench
{
    /**
     * Override application aliases.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function overrideApplicationProviders($app): array
    {
        return [
            'Illuminate\Auth\AuthServiceProvider' => 'Orchestra\Auth\AuthServiceProvider',
        ];
    }
}
