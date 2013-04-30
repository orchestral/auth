<?php namespace Orchestra\Auth\Tests\Acl;

use Mockery as m;
use Orchestra\Auth\Acl\Container;
use Orchestra\Memory\Drivers\Runtime as Memory;

class ContainerTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Application mock instance.
	 *
	 * @var Illuminate\Foundation\Application
	 */
	private $app = null;

	/**
	 * Acl Container instance.
	 *
	 * @var Orchestra\Auth\Acl\Container
	 */
	private $stub = null;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{ 
		$this->app = array(
			'auth'   => $auth = m::mock('\Orchestra\Auth\Guard'),
			'config' => $config = m::mock('Config'),
			'events' => $event = m::mock('Event'),
		);

		$auth->shouldReceive('guest')->andReturn(true)
			->shouldReceive('user')->andReturnNull();
		$config->shouldReceive('get')->andReturn(array());
		$event->shouldReceive('until')->andReturn(array('admin', 'editor'));

		$runtime = new Memory($this->app);
		$runtime->put('acl_foo', static::providerMemory());

		$this->stub = new Container($this->app['auth'], 'foo', $runtime);
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($this->stub);

		m::close();
	}

	/**
	 * Add data provider
	 * 
	 * @return array
	 */
	public static function providerMemory()
	{
		return array(
			'acl'     => array('0:0' => false, '0:1' => false, '1:0' => true, '1:1' => true),
			'actions' => array('Manage User', 'Manage'),
			'roles'   => array('Guest', 'Admin'),
		);
	}

	/**
	 * Test instance of stub.
	 *
	 * @test
	 */
	public function testInstanceOfStub()
	{
		$refl    = new \ReflectionObject($this->stub);
		$memory  = $refl->getProperty('memory');
		$roles   = $refl->getProperty('roles');
		$actions = $refl->getProperty('actions');
		$acl     = $refl->getProperty('acl');

		$memory->setAccessible(true);
		$roles->setAccessible(true);
		$actions->setAccessible(true);
		$acl->setAccessible(true);

		$this->assertInstanceOf('\Orchestra\Auth\Acl\Container', $this->stub);
		$this->assertInstanceOf('\Orchestra\Memory\Drivers\Runtime', $memory->getValue($this->stub));
		$this->assertInstanceOf('\Orchestra\Auth\Acl\Fluent', $roles->getValue($this->stub));
		$this->assertInstanceOf('\Orchestra\Auth\Acl\Fluent', $actions->getValue($this->stub));
		$this->assertTrue(is_array($acl->getValue($this->stub)));
	}

	/**
	 * Test sync memory.
	 *
	 * @test
	 */
	public function testSyncMemoryAfterConstruct()
	{
		$runtime = new Memory($this->app, 'foo');
		$runtime->put('acl_foo', static::providerMemory());

		$stub = new Container($this->app['auth'], 'foo');

		$this->assertFalse($stub->attached());

		$stub->attach($runtime);

		$this->assertTrue($stub->attached());

		$stub->addRole('foo');
		$stub->addAction('foobar');
		$stub->allow('foo', 'foobar');

		$refl    = new \ReflectionObject($stub);
		$memory  = $refl->getProperty('memory');
		$roles   = $refl->getProperty('roles');
		$actions = $refl->getProperty('actions');
		$acl     = $refl->getProperty('acl');

		$memory->setAccessible(true);
		$roles->setAccessible(true);
		$actions->setAccessible(true);
		$acl->setAccessible(true);

		$expected = array('guest', 'admin', 'foo');
		$this->assertEquals($expected, $roles->getValue($stub)->get());
		$this->assertEquals($expected, $memory->getValue($stub)->get('acl_foo.roles'));
		$this->assertEquals($expected, $runtime->get('acl_foo.roles'));

		$expected = array('manage-user', 'manage', 'foobar');
		$this->assertEquals($expected, $actions->getValue($stub)->get());
		$this->assertEquals($expected, $memory->getValue($stub)->get('acl_foo.actions'));
		$this->assertEquals($expected, $runtime->get('acl_foo.actions'));

		$expected = array('0:0' => false, '0:1' => false, '1:0' => true, '1:1' => true, '2:2' => true);
		$this->assertEquals($expected, $acl->getValue($stub));
		$this->assertEquals($expected, $memory->getValue($stub)->get('acl_foo.acl'));
		$this->assertEquals($expected, $runtime->get('acl_foo.acl'));
	}

	/**
	 * Test Orchestra\Auth\Acl\Container::attach() method throws exception
	 * when attaching multiple memory instance.
	 *
	 * @expectedException \RuntimeException
	 */
	public function testAttachMethodThrowsExceptionWhenAttachMultipleMemory()
	{
		$runtime = new Memory($this->app, 'foo');
		$runtime->put('acl_foo', static::providerMemory());

		$stub = new Container($this->app['auth'] ,'foo', $runtime);
		$stub->attach($runtime);
	}

	/**
	 * Test Orchestra\Auth\Acl\Container::allow() method.
	 *
	 * @test
	 */
	public function testAllowMethod()
	{
		$runtime = new Memory($this->app, 'foo');
		$runtime->put('acl_foo', static::providerMemory());

		$stub = new Container($this->app['auth'], 'foo', $runtime);

		$stub->allow('guest', 'manage-user');

		$refl   = new \ReflectionObject($this->stub);
		$memory = $refl->getProperty('memory');
		$acl    = $refl->getProperty('acl');

		$memory->setAccessible(true);
		$acl->setAccessible(true);

		$expected = array('0:0' => true, '0:1' => false, '1:0' => true, '1:1' => true);
		$this->assertEquals($expected, $acl->getValue($stub));
		$this->assertEquals($expected, $memory->getValue($stub)->get('acl_foo.acl'));
		$this->assertEquals($expected, $runtime->get('acl_foo.acl'));
	}

	/**
	 * Test Orchestra\Auth\Acl\Container::deny() method.
	 *
	 * @test
	 */
	public function testDenyMethod()
	{
		$runtime = new Memory($this->app, 'foo');
		$runtime->put('acl_foo', static::providerMemory());

		$stub = new Container($this->app['auth'], 'foo', $runtime);

		$stub->deny('admin', 'manage-user');

		$refl   = new \ReflectionObject($this->stub);
		$memory = $refl->getProperty('memory');
		$acl    = $refl->getProperty('acl');

		$memory->setAccessible(true);
		$acl->setAccessible(true);

		$expected = array('0:0' => false, '0:1' => false, '1:0' => false, '1:1' => true);
		$this->assertEquals($expected, $acl->getValue($stub));
		$this->assertEquals($expected, $memory->getValue($stub)->get('acl_foo.acl'));
		$this->assertEquals($expected, $runtime->get('acl_foo.acl'));
	}

	/**
	 * Test Orchestra\Auth\Acl\Container::can() method.
	 *
	 * @test
	 */
	public function testCanMethod()
	{
		$runtime = new Memory($this->app, 'foo');
		$runtime->put('acl_foo', static::providerMemory());

		$stub = new Container($this->app['auth'], 'foo', $runtime);

		$stub->addActions(array('Manage Page', 'Manage Photo'));
		$stub->allow('guest', 'Manage Page');

		$this->assertFalse($stub->can('manage'));
		$this->assertTrue($stub->can('manage-page'));
		$this->assertFalse($stub->can('manage-photo'));
	}

	/**
	 * Test Orchestra\Auth\Acl\Container::check() method.
	 *
	 * @test
	 */
	public function testCheckMethod()
	{
		$runtime = new Memory($this->app, 'foo');
		$runtime->put('acl_foo', static::providerMemory());

		$stub = new Container($this->app['auth'], 'foo', $runtime);

		$stub->addActions(array('Manage Page', 'Manage Photo'));
		$stub->allow('guest', 'Manage Page');

		$this->assertFalse($stub->check('guest', 'manage'));
		$this->assertTrue($stub->check('guest', 'manage-page'));
		$this->assertFalse($stub->check('guest', 'manage-photo'));
	}

	/**
	 * Test Orchestra\Auth\Acl\Container::check() method throws exception.
	 *
	 * @expectedException InvalidArgumentException
	 */
	public function testCheckMethodUsingMockOneThrowsException()
	{
		$this->stub->check('guest', 'view foo');
	}

	/**
	 * Test Orchestra\Auth\Acl\Container::allow() method throws exception
	 * for roles.
	 *
	 * @expectedException InvalidArgumentException
	 */
	public function testAllowMethodUsingMockOneThrowsExceptionForRoles()
	{
		$this->stub->allow('boss', 'view blog');
	}

	/**
	 * Test Orchestra\Auth\Acl\Container::allow() method throws exception
	 * for actions.
	 *
	 * @expectedException InvalidArgumentException
	 */
	public function testAllowMethodUsingMockOneThrowsExceptionForActions()
	{
		$this->stub->allow('guest', 'view foo');
	}

	/**
	 * Test memory is properly sync during construct.
	 *
	 * @test
	 */
	public function testMemoryIsProperlySync()
	{
		$stub    = $this->stub;
		$refl    = new \ReflectionObject($stub);
		$memory  = $refl->getProperty('memory');
		$roles   = $refl->getProperty('roles');
		$actions = $refl->getProperty('actions');

		$memory->setAccessible(true);
		$roles->setAccessible(true);
		$actions->setAccessible(true);

		$this->assertInstanceOf('\Orchestra\Memory\Drivers\Runtime', $memory->getValue($stub));

		$this->assertInstanceOf('\Orchestra\Auth\Acl\Fluent', $roles->getValue($stub));

		$this->assertTrue($stub->roles()->has('guest'));
		$this->assertTrue($stub->roles()->has('admin'));
		$this->assertTrue($stub->hasRole('guest'));
		$this->assertTrue($stub->hasRole('admin'));
		$this->assertEquals(array('guest', 'admin'), $roles->getValue($stub)->get());
		$this->assertEquals(array('guest', 'admin'), $stub->roles()->get());

		$this->assertInstanceOf('\Orchestra\Auth\Acl\Fluent', $actions->getValue($stub));

		$this->assertTrue($stub->actions()->has('manage-user'));
		$this->assertTrue($stub->actions()->has('manage'));
		$this->assertTrue($stub->hasAction('manage-user'));
		$this->assertTrue($stub->hasAction('manage'));
		$this->assertEquals(array('manage-user', 'manage'), $actions->getValue($stub)->get());
		$this->assertEquals(array('manage-user', 'manage'), $stub->actions()->get());
	}

	/**
	 * Test adding duplicate roles and actions is properly handled
	 *
	 * @test
	 */
	public function testAddDuplicates()
	{
		$runtime = new Memory($this->app, 'foo');
		$runtime->put('acl_foo', static::providerMemory()); 

		$stub    = new Container($this->app['auth'], 'foo', $runtime);
		$refl    = new \ReflectionObject($stub);
		$actions = $refl->getProperty('actions');
		$roles   = $refl->getProperty('roles');

		$actions->setAccessible(true);
		$roles->setAccessible(true);

		$stub->roles()->add('admin');
		$stub->roles()->fill(array('admin'));
		$stub->addRole('admin');
		$stub->addRoles(array('admin'));

		$stub->actions()->add('manage');
		$stub->actions()->fill(array('manage'));
		$stub->addAction('manage');
		$stub->addActions(array('manage'));

		$this->assertEquals(array('guest', 'admin'), $roles->getValue($stub)->get());
		$this->assertEquals(array('guest', 'admin'), $stub->roles()->get());

		$this->assertEquals(array('manage-user', 'manage'), $actions->getValue($stub)->get());
		$this->assertEquals(array('manage-user', 'manage'), $stub->actions()->get());
	}
}
