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

		$appMock = \Mockery::mock('Application')
			->shouldReceive('instance')->andReturn(true);
		\Illuminate\Support\Facades\Event::setFacadeApplication(
			$appMock->getMock()
		);
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
		$eventMock = \Mockery::mock('Event')
			->shouldReceive('until')
				->with('orchestra.auth: roles', \Mockery::any())
				->once()
				->andReturn(array('admin', 'editor'));
		
		\Illuminate\Support\Facades\Event::swap($eventMock->getMock());

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
		$eventMock = \Mockery::mock('Event')
			->shouldReceive('until')
				->with('orchestra.auth: roles', \Mockery::any())
				->once()
				->andReturn(array('admin', 'editor'));
		
		\Illuminate\Support\Facades\Event::swap($eventMock->getMock());

		$stub = new \Orchestra\Auth\Guard(
			$this->provider,
			$this->session
		);

		$this->assertTrue($stub->is('admin'));
		$this->assertTrue($stub->is('editor'));
		$this->assertFalse($stub->is('user'));
	}

}