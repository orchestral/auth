<?php namespace Orchestra\Auth\Tests;

class GuardTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Provider instance.
	 * 
	 * @var Illuminate\Auth\UserProviderInterface
	 */
	private $provider = null;

	/**
	 * Session instance.
	 * 
	 * @var Illuminate\Session\Store
	 */
	private $session = null;

	/**
	 * Event dispatcher instance.
	 * 
	 * @var Illuminate\Event\Dispatcher
	 */
	private $events = null;

	/**
	 * Setup the test environment
	 */
	public function setUp()
	{
		$this->provider = \Mockery::mock('\Illuminate\Auth\UserProviderInterface');
		$this->session  = \Mockery::mock('\Illuminate\Session\Store');
		$this->events   = \Mockery::mock('\Illuminate\Events\Dispatcher');
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		\Mockery::close();
	}

	/**
	 * Test Orchestra\Auth\Guard::roles() returning valid roles
	 * 
	 * @test
	 */
	public function testRolesMethod()
	{
		$events = $this->events;

		$events->shouldReceive('until')
				->with('orchestra.auth: roles', \Mockery::any())
				->once()
				->andReturn(array('admin', 'editor'));

		$stub = new \Orchestra\Auth\Guard(
			$this->provider,
			$this->session
		);

		$stub->setDispatcher($events);

		$expected = array('admin', 'editor');
		$output   = $stub->roles();

		$this->assertEquals($expected, $output);
	}

	/**
	 * Test Orchestra\Support\Auth::is() returning valid roles
	 * 
	 * @test
	 * @group support
	 */
	public function testIsMethod()
	{
		$events = $this->events;

		$events->shouldReceive('until')
				->with('orchestra.auth: roles', \Mockery::any())
				->once()
				->andReturn(array('admin', 'editor'));

		$stub = new \Orchestra\Auth\Guard(
			$this->provider,
			$this->session
		);

		$stub->setDispatcher($events);

		$this->assertTrue($stub->is('admin'));
		$this->assertTrue($stub->is('editor'));
		$this->assertFalse($stub->is('user'));
	}

}