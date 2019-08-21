<?php

namespace Orchestra\Authorization\TestCase\Unit;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Orchestra\Authorization\Factory;

class FactoryTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /**
     * Test Orchestra\Authorization\Factory::make().
     *
     * @test
     */
    public function testMakeMethod()
    {
        $auth = m::mock('Illuminate\Contracts\Auth\Guard');
        $stub = new Factory($auth);

        $this->assertInstanceOf('\Orchestra\Authorization\Authorization', $stub->make('mock-one'));

        $memory = m::mock('\Orchestra\Memory\Provider');
        $memory->shouldReceive('get')->once()->andReturn([])
            ->shouldReceive('put')->once()->andReturn([]);

        $this->assertInstanceOf('\Orchestra\Authorization\Authorization', $stub->make('mock-two', $memory));
    }

    /**
     * Test Orchestra\Authorization\Factory::register() method.
     *
     * @test
     */
    public function testRegisterMethod()
    {
        $auth = m::mock('Illuminate\Contracts\Auth\Guard');
        $stub = new Factory($auth);

        $auth->shouldReceive('guest')->times(3)->andReturn(true);

        $stub->register(function ($acl) {
            $acl->addActions(['view blog', 'view forum', 'view news']);
            $acl->allow('guest', ['view blog']);
            $acl->deny('guest', 'view forum');
        });

        $acl = $stub->make(null);
        $this->assertInstanceOf('\Orchestra\Authorization\Authorization', $acl);
        $this->assertTrue($acl->can('view blog'));
        $this->assertFalse($acl->can('view forum'));
        $this->assertFalse($acl->can('view news'));
    }

    /**
     * Test Orchestra\Authorization\Factory magic methods.
     *
     * @test
     */
    public function testMagicMethods()
    {
        $auth = m::mock('Illuminate\Contracts\Auth\Guard');
        $stub = new Factory($auth);

        $acl1 = $stub->make('mock-one');
        $acl2 = $stub->make('mock-two');

        $stub->addRoles(['admin', 'manager', 'moderator']);
        $stub->removeRoles(['moderator']);

        $this->assertTrue($acl1->hasRole('admin'));
        $this->assertTrue($acl2->hasRole('admin'));
        $this->assertTrue($acl1->hasRole('manager'));
        $this->assertTrue($acl2->hasRole('manager'));

        $stub->removeRole('manager');

        $this->assertTrue($acl1->hasRole('admin'));
        $this->assertTrue($acl2->hasRole('admin'));
        $this->assertFalse($acl1->hasRole('manager'));
        $this->assertFalse($acl2->hasRole('manager'));

        $this->assertTrue(is_array($stub->all()));
        $this->assertFalse([] === $stub->all());

        $stub->finish();

        $this->assertEquals([], $stub->all());
    }

    /**
     * Test Orchestra\Authorization\Factory::all() method.
     *
     * @test
     */
    public function testAllMethod()
    {
        $auth = m::mock('Illuminate\Contracts\Auth\Guard');
        $stub = new Factory($auth);

        $mock1 = $stub->make('mock-one');
        $mock2 = $stub->make('mock-two');
        $mock3 = $stub->make('mock-three');

        $expect = ['mock-one', 'mock-two', 'mock-three'];
        $this->assertEquals($expect, array_keys($stub->all()));

        $this->assertEquals($mock1, $stub->get('mock-one'));
        $this->assertEquals($mock2, $stub->get('mock-two'));
        $this->assertEquals($mock3, $stub->get('mock-three'));
        $this->assertNull($stub->get('mock-four'));
    }
}
