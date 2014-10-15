<?php namespace Orchestra\Auth\Tests\Acl;

use Mockery as m;
use Orchestra\Auth\Acl\Factory;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Orchestra\Auth\Acl\Factory::make()
     *
     * @test
     */
    public function testMakeMethod()
    {
        $auth = m::mock('\Orchestra\Auth\Guard');
        $stub = new Factory($auth);

        $this->assertInstanceOf('\Orchestra\Auth\Acl\Acl', $stub->make('mock-one'));

        $memory = m::mock('\Orchestra\Memory\Provider');
        $memory->shouldReceive('get')->once()->andReturn(array())
            ->shouldReceive('put')->once()->andReturn(array());

        $this->assertInstanceOf('\Orchestra\Auth\Acl\Acl', $stub->make('mock-two', $memory));
    }

    /**
     * Test Orchestra\Auth\Acl\Factory::register() method.
     *
     * @test
     */
    public function testRegisterMethod()
    {
        $auth = m::mock('\Orchestra\Auth\Guard');
        $stub = new Factory($auth);

        $auth->shouldReceive('guest')->times(3)->andReturn(true);

        $stub->register(function ($acl) {
            $acl->addActions(array('view blog', 'view forum', 'view news'));
            $acl->allow('guest', array('view blog'));
            $acl->deny('guest', 'view forum');
        });

        $acl = $stub->make(null);
        $this->assertInstanceOf('\Orchestra\Auth\Acl\Acl', $acl);
        $this->assertTrue($acl->can('view blog'));
        $this->assertFalse($acl->can('view forum'));
        $this->assertFalse($acl->can('view news'));
    }

    /**
     * Test Orchestra\Auth\Acl\Factory magic methods.
     *
     * @test
     */
    public function testMagicMethods()
    {
        $auth = m::mock('\Orchestra\Auth\Guard');
        $stub = new Factory($auth);

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
     * Test Orchestra\Auth\Acl\Factory::all() method.
     *
     * @test
     */
    public function testAllMethod()
    {
        $auth = m::mock('\Orchestra\Auth\Guard');
        $stub = new Factory($auth);

        $mock1 = $stub->make('mock-one');
        $mock2 = $stub->make('mock-two');
        $mock3 = $stub->make('mock-three');

        $expect = array('mock-one', 'mock-two', 'mock-three');
        $this->assertEquals($expect, array_keys($stub->all()));

        $this->assertEquals($mock1, $stub->get('mock-one'));
        $this->assertEquals($mock2, $stub->get('mock-two'));
        $this->assertEquals($mock3, $stub->get('mock-three'));
        $this->assertNull($stub->get('mock-four'));
    }
}
