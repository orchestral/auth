<?php namespace Orchestra\Auth\Tests\Acl;

use Mockery as m;
use Orchestra\Auth\Acl\Environment;

class EnvironmentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Orchestra\Auth\Acl\Environment::make()
     *
     * @test
     */
    public function testMakeMethod()
    {
        $auth = m::mock('\Orchestra\Auth\Guard');
        $stub = new Environment($auth);

        $this->assertInstanceOf('\Orchestra\Auth\Acl\Container', $stub->make('mock-one'));

        $memory = m::mock('\Orchestra\Memory\Drivers\Driver');
        $memory->shouldReceive('get')->once()->andReturn(array())
            ->shouldReceive('put')->times(3)->andReturn(array());

        $this->assertInstanceOf('\Orchestra\Auth\Acl\Container', $stub->make('mock-two', $memory));
    }

    /**
     * Test Orchestra\Auth\Acl\Environment::register() method.
     *
     * @test
     */
    public function testRegisterMethod()
    {
        $auth = m::mock('\Orchestra\Auth\Guard');
        $stub = new Environment($auth);

        $auth->shouldReceive('guest')->times(3)->andReturn(true);

        $stub->register(function ($acl) {
            $acl->addActions(array('view blog', 'view forum', 'view news'));
            $acl->allow('guest', array('view blog'));
            $acl->deny('guest', 'view forum');
        });

        $acl = $stub->make(null);
        $this->assertInstanceOf('\Orchestra\Auth\Acl\Container', $acl);
        $this->assertTrue($acl->can('view blog'));
        $this->assertFalse($acl->can('view forum'));
        $this->assertFalse($acl->can('view news'));
    }

    /**
     * Test Orchestra\Auth\Acl\Environment magic methods.
     *
     * @test
     */
    public function testMagicMethods()
    {
        $auth = m::mock('\Orchestra\Auth\Guard');
        $stub = new Environment($auth);

        $acl1 = $stub->make('mock-one');
        $acl2 = $stub->make('mock-two');

        $stub->addRoles(array('admin', 'manager', 'moderator'));
        $stub->removeRoles(array('moderator'));

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
        $this->assertFalse(array() === $stub->all());

        $stub->finish();

        $this->assertEquals(array(), $stub->all());
    }

    /**
     * Test Orchestra\Auth\Acl\Environment::all() method.
     *
     * @test
     */
    public function testAllMethod()
    {
        $auth = m::mock('\Orchestra\Auth\Guard');
        $stub = new Environment($auth);

        $stub->make('mock-one');
        $stub->make('mock-two');
        $stub->make('mock-three');

        $expect = array('mock-one', 'mock-two', 'mock-three');
        $output = $stub->all();

        $this->assertEquals($expect, array_keys($output));
    }
}
