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
	 * Test Orchestra\Auth\Guard::roles() method returning valid roles.
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
	 * Test Orchestra\Support\Auth::is() method returning valid roles.
	 * 
	 * @test
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

	/**
	 * Test Orchestra\Support\Auth::logout() method.
	 * 
	 * @test
	 */
	public function testLogoutMethod()
	{
		$events = $this->events;
		$session = $this->session;

		$events->shouldReceive('until')
				->with('orchestra.auth: roles', \Mockery::any())->never()
				->andReturn(array('admin', 'editor'))
			->shouldReceive('fire')
				->with('auth.logout', \Mockery::any())->once()
				->andReturn(array('admin', 'editor'));
		$session->shouldReceive('forget')->once()->andReturn(null);

		$stub    = new \Orchestra\Auth\Guard(
			$this->provider,
			$this->session
		);

		$stub->setDispatcher($events);
		$stub->setCookieJar($cookie = \Mockery::mock('\Illuminate\Cookie\CookieJar'));
		$cookie->shouldReceive('forget')->once()->andReturn(null);

		$refl      = new \ReflectionObject($stub);
		$user      = $refl->getProperty('user');
		$userRoles = $refl->getProperty('userRoles');

		$user->setAccessible(true);
		$userRoles->setAccessible(true);

		$user->setValue($stub, (object) array('id' => 1));
		$userRoles->setValue($stub, array(1 => array('admin', 'editor')));

		$this->assertEquals(array('admin', 'editor'), $stub->roles());

		$stub->logout();

		$this->assertNull($userRoles->getValue($stub));
	}

}