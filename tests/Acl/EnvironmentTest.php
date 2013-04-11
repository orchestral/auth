<?php namespace Orchestra\Auth\Tests\Acl;

class EnvironmentTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		\Mockery::close();
	}

	/**
	 * Test Orchestra\Auth\Acl\Environment::make()
	 *
	 * @test
	 */
	public function testMakeMethod()
	{
		$stub = new \Orchestra\Auth\Acl\Environment;

		$this->assertInstanceOf('\Orchestra\Auth\Acl\Container', $stub->make('mock-one'));

		$memoryMock = \Mockery::mock('\Orchestra\Memory\Drivers\Driver')
			->shouldReceive('get')
				->once()
				->andReturn(array())
			->shouldReceive('put')
				->times(3)
				->andReturn(array());

		$this->assertInstanceOf('\Orchestra\Auth\Acl\Container', 
			$stub->make('mock-two', $memoryMock->getMock()));
	}

	/**
	 * Test Orchestra\Auth\Acl\Environment::register() method.
	 *
	 * @test
	 */
	public function testRegisterMethod()
	{
		$stub = new \Orchestra\Auth\Acl\Environment;

		$stub->register(function ($acl)
		{
			$acl->add_actions(array('view blog', 'view forum', 'view news'));
			$acl->allow('guest', array('view blog'));
			$acl->deny('guest', 'view forum');
		});

		$acl = $stub->make(null);
		$this->assertInstanceOf('\Orchestra\Auth\Acl\Container', $acl);

		$output = $acl->can('view blog');
		$this->assertTrue($output);
		
		$output = $acl->can('view forum');
		$this->assertFalse($output);

		$output = $acl->can('view news');
		$this->assertFalse($output);
	}

	/**
	 * Test Orchestra\Auth\Acl\Environment magic methods.
	 *
	 * @test
	 */
	public function testMagicMethods()
	{
		$memoryMock = \Mockery::mock()
			->shouldReceive('shutdown')
				->once()
				->andReturn(true);

		\Orchestra\Memory\Facade::swap($memoryMock->getMock());

		$stub = new \Orchestra\Auth\Acl\Environment;

		$acl1 = $stub->make('mock-one');
		$acl2 = $stub->make('mock-two');

		$stub->add_role('admin');
		$stub->add_role('manager');

		$this->assertTrue($acl1->has_role('admin'));
		$this->assertTrue($acl2->has_role('admin'));
		$this->assertTrue($acl1->has_role('manager'));
		$this->assertTrue($acl2->has_role('manager'));

		$stub->remove_role('manager');

		$this->assertTrue($acl1->has_role('admin'));
		$this->assertTrue($acl2->has_role('admin'));
		$this->assertFalse($acl1->has_role('manager'));
		$this->assertFalse($acl2->has_role('manager'));

		$this->assertTrue(is_array($stub->all()));
		$this->assertFalse(array() === $stub->all());

		$stub->shutdown();

		$this->assertEquals(array(), $stub->all());
	}

	/**
	 * Test Orchestra\Auth\Acl\Environment::all() method.
	 *
	 * @test
	 */
	public function testAllMethod()
	{
		$stub = new \Orchestra\Auth\Acl\Environment;
		$stub->make('mock-one');
		$stub->make('mock-two');
		$stub->make('mock-three');

		$expect = array('mock-one', 'mock-two', 'mock-three');
		$output = $stub->all();

		$this->assertEquals($expect, array_keys($output));
	}
}