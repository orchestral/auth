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
	 * Setup the test environment
	 */
	public function setUp()
	{
		$this->provider = \Mockery::mock('Illuminate\Auth\UserProviderInterface');
		$this->session = \Mockery::mock('Illuminate\Session\Store');

		\Illuminate\Support\Facades\Event::setFacadeApplication($app = \Mockery::mock('Application'));
		$app->shouldReceive('instance')->andReturn(true);
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
		\Illuminate\Support\Facades\Event::swap($event = \Mockery::mock('Event'));

		$event->shouldReceive('until')
				->with('orchestra.auth: roles', \Mockery::any())
				->once()
				->andReturn(array('admin', 'editor'));

		$stub = new \Orchestra\Auth\Guard(
			$this->provider,
			$this->session
		);

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
		\Illuminate\Support\Facades\Event::swap($event = \Mockery::mock('Event'));

		$event->shouldReceive('until')
				->with('orchestra.auth: roles', \Mockery::any())
				->once()
				->andReturn(array('admin', 'editor'));

		$stub = new \Orchestra\Auth\Guard(
			$this->provider,
			$this->session
		);

		$this->assertTrue($stub->is('admin'));
		$this->assertTrue($stub->is('editor'));
		$this->assertFalse($stub->is('user'));
	}

}