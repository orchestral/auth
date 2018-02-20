<?php

namespace Orchestra\Authorization\TestCase\Unit;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Orchestra\Authorization\Fluent;

class FluentTest extends TestCase
{
    /**
     * Stub instance.
     *
     * @return \Orchestra\Authorization\Fluent
     */
    private $stub;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        $this->stub = new class() extends Fluent {
            protected $name = 'stub';
        };

        $this->stub->attach(['Hello World']);
    }

    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        unset($this->stub);

        m::close();
    }

    /**
     * @test
     */
    public function it_has_proper_signature()
    {
        $this->assertInstanceOf('\Orchestra\Authorization\Fluent', $this->stub);

        $refl = new \ReflectionObject($this->stub);
        $name = $refl->getProperty('name');
        $name->setAccessible(true);

        $this->assertEquals('stub', $name->getValue($this->stub));
    }

    /**
     * @test
     */
    public function it_can_check_whether_fluent_has_item()
    {
        $this->assertTrue($this->stub->has('hello-world'));
        $this->assertFalse($this->stub->has('goodbye-world'));
    }
}
