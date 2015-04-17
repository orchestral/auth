<?php namespace Orchestra\Authorization\TestCase;

use Mockery as m;
use Orchestra\Memory\Provider;
use Orchestra\Memory\Handlers\Runtime;
use Orchestra\Authorization\Authorization;
use Illuminate\Container\Container as IlluminateContainer;

class AuthorizationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Application mock instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    private $app = null;

    /**
     * Acl Container instance.
     *
     * @var Orchestra\Authorization\Authorization
     */
    private $stub = null;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->app           = new IlluminateContainer();
        $this->app['auth']   = $auth   = m::mock('\Orchestra\Contracts\Auth\Guard');
        $this->app['config'] = $config = m::mock('\Illuminate\Contracts\Config\Repository');
        $this->app['events'] = $event = m::mock('\Illuminate\Contracts\Events\Dispatcher');

        $auth->shouldReceive('guest')->andReturn(true)
            ->shouldReceive('user')->andReturn(null);
        $config->shouldReceive('get')->andReturn([]);
        $event->shouldReceive('until')->andReturn(['admin', 'editor']);

        $memory  = new Provider(new Runtime('foo', []));
        $memory->put('acl_foo', $this->memoryProvider());

        $this->stub = new Authorization($this->app['auth'], 'foo', $memory);
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
     * Get runtime memory provider.
     *
     * @return \Orchestra\Memory\Provider
     */
    protected function getRuntimeMemoryProvider()
    {
        return new Provider(new Runtime('foo', []));
    }

    /**
     * Add data provider.
     *
     * @return array
     */
    protected function memoryProvider()
    {
        return [
            'acl'     => ['0:0' => false, '0:1' => false, '1:0' => true, '1:1' => true],
            'actions' => ['Manage User', 'Manage'],
            'roles'   => ['Guest', 'Admin'],
        ];
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

        $this->assertInstanceOf('\Orchestra\Authorization\Authorization', $this->stub);
        $this->assertInstanceOf('\Orchestra\Contracts\Memory\Provider', $memory->getValue($this->stub));
        $this->assertInstanceOf('\Orchestra\Authorization\Fluent', $roles->getValue($this->stub));
        $this->assertInstanceOf('\Orchestra\Authorization\Fluent', $actions->getValue($this->stub));
        $this->assertTrue(is_array($acl->getValue($this->stub)));
    }

    /**
     * Test sync memory.
     *
     * @test
     */
    public function testSyncMemoryAfterConstruct()
    {
        $runtime = $this->getRuntimeMemoryProvider();
        $runtime->put('acl_foo', $this->memoryProvider());

        $stub = new Authorization($this->app['auth'], 'foo');

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

        $expected = ['guest', 'admin', 'foo'];
        $this->assertEquals($expected, $roles->getValue($stub)->get());
        $this->assertEquals($expected, $memory->getValue($stub)->get('acl_foo.roles'));
        $this->assertEquals($expected, $runtime->get('acl_foo.roles'));
        $this->assertInstanceOf('\Orchestra\Authorization\Fluent', $stub->roles());

        $expected = ['manage-user', 'manage', 'foobar'];
        $this->assertEquals($expected, $actions->getValue($stub)->get());
        $this->assertEquals($expected, $memory->getValue($stub)->get('acl_foo.actions'));
        $this->assertEquals($expected, $runtime->get('acl_foo.actions'));
        $this->assertInstanceOf('\Orchestra\Authorization\Fluent', $stub->actions());

        $expected = ['0:0' => false, '0:1' => false, '1:0' => true, '1:1' => true, '2:2' => true];
        $this->assertEquals($expected, $acl->getValue($stub));
        $this->assertEquals($expected, $memory->getValue($stub)->get('acl_foo.acl'));
        $this->assertEquals($expected, $runtime->get('acl_foo.acl'));
        $this->assertEquals($expected, $stub->acl());
    }

    /**
     * Test Orchestra\Authorization\Authorization::attach() method throws exception
     * when attaching multiple memory instance.
     *
     * @expectedException \RuntimeException
     */
    public function testAttachMethodThrowsExceptionWhenAttachMultipleMemory()
    {
        $runtime1 = $this->getRuntimeMemoryProvider();
        $runtime1->put('acl_foo', $this->memoryProvider());

        $runtime2 = new Provider(new Runtime('foobar', []));

        $stub = new Authorization($this->app['auth'], 'foo', $runtime1);
        $stub->attach($runtime2);
    }

    /**
     * Test Orchestra\Authorization\Authorization::attach() method don't throw
     * exception when attaching multiple memory instance using the same
     * instance.
     *
     * @test
     */
    public function testAttachMethodWhenAttachMultipleMemoryUsingTheSameInstance()
    {
        $runtime = $this->getRuntimeMemoryProvider();
        $runtime->put('acl_foo', $this->memoryProvider());

        $stub = new Authorization($this->app['auth'], 'foo', $runtime);
        $stub->attach($runtime);
    }

    /**
     * Test Orchestra\Authorization\Authorization::allow() method.
     *
     * @test
     */
    public function testAllowMethod()
    {
        $runtime = $this->getRuntimeMemoryProvider();
        $runtime->put('acl_foo', $this->memoryProvider());

        $stub = new Authorization($this->app['auth'], 'foo', $runtime);

        $stub->allow('guest', 'manage-user');

        $refl   = new \ReflectionObject($this->stub);
        $memory = $refl->getProperty('memory');
        $acl    = $refl->getProperty('acl');

        $memory->setAccessible(true);
        $acl->setAccessible(true);

        $expected = ['0:0' => true, '0:1' => false, '1:0' => true, '1:1' => true];
        $this->assertEquals($expected, $acl->getValue($stub));
        $this->assertEquals($expected, $memory->getValue($stub)->get('acl_foo.acl'));
        $this->assertEquals($expected, $runtime->get('acl_foo.acl'));
    }

    /**
     * Test Orchestra\Authorization\Authorization::deny() method.
     *
     * @test
     */
    public function testDenyMethod()
    {
        $runtime = $this->getRuntimeMemoryProvider();
        $runtime->put('acl_foo', $this->memoryProvider());

        $stub = new Authorization($this->app['auth'], 'foo', $runtime);

        $stub->deny('admin', 'manage-user');

        $refl   = new \ReflectionObject($this->stub);
        $memory = $refl->getProperty('memory');
        $acl    = $refl->getProperty('acl');

        $memory->setAccessible(true);
        $acl->setAccessible(true);

        $expected = ['0:0' => false, '0:1' => false, '1:0' => false, '1:1' => true];
        $this->assertEquals($expected, $acl->getValue($stub));
        $this->assertEquals($expected, $memory->getValue($stub)->get('acl_foo.acl'));
        $this->assertEquals($expected, $runtime->get('acl_foo.acl'));
    }

    /**
     * Test Orchestra\Authorization\Authorization::can() method as "admin" user.
     *
     * @test
     */
    public function testCanMethodAsAdminUser()
    {
        $auth = m::mock('\Orchestra\Contracts\Auth\Guard');

        $auth->shouldReceive('guest')->times(4)->andReturn(false)
            ->shouldReceive('roles')->times(4)->andReturn(['Admin']);

        $runtime = $this->getRuntimeMemoryProvider();
        $runtime->put('acl_foo', $this->memoryProvider());

        $stub = new Authorization($auth, 'foo', $runtime);

        $stub->addActions(['Manage Page', 'Manage Photo']);
        $stub->allow('guest', 'Manage Page');

        $this->assertTrue($stub->can('manage'));
        $this->assertTrue($stub->can('manage user'));
        $this->assertFalse($stub->can('manage-page'));
        $this->assertFalse($stub->can('manage-photo'));
    }

    /**
     * Test Orchestra\Authorization\Authorization::can() method as "guest" user.
     *
     * @test
     */
    public function testCanMethodAsGuestUser()
    {
        $runtime = $this->getRuntimeMemoryProvider();
        $runtime->put('acl_foo', $this->memoryProvider());

        $stub = new Authorization($this->app['auth'], 'foo', $runtime);

        $stub->addActions(['Manage Page', 'Manage Photo']);
        $stub->allow('guest', 'Manage Page');

        $this->assertFalse($stub->can('manage'));
        $this->assertTrue($stub->can('manage-page'));
        $this->assertFalse($stub->can('manage-photo'));
    }

    /**
     * Test Orchestra\Authorization\Authorization::check() method.
     *
     * @test
     */
    public function testCheckMethod()
    {
        $runtime = $this->getRuntimeMemoryProvider();
        $runtime->put('acl_foo', $this->memoryProvider());

        $stub = new Authorization($this->app['auth'], 'foo', $runtime);

        $stub->addActions(['Manage Page', 'Manage Photo']);
        $stub->allow('guest', 'Manage Page');

        $this->assertFalse($stub->check('guest', 'manage'));
        $this->assertTrue($stub->check('guest', 'manage-page'));
        $this->assertFalse($stub->check('guest', 'manage-photo'));
    }

    /**
     * Test Orchestra\Authorization\Authorization::check() method throws exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testCheckMethodUsingMockOneThrowsException()
    {
        $this->stub->check('guest', 'view foo');
    }

    /**
     * Test Orchestra\Authorization\Authorization::allow() method throws exception
     * for roles.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testAllowMethodUsingMockOneThrowsExceptionForRoles()
    {
        $this->stub->allow('boss', 'view blog');
    }

    /**
     * Test Orchestra\Authorization\Authorization::allow() method throws exception
     * for actions.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testAllowMethodUsingMockOneThrowsExceptionForActions()
    {
        $this->stub->allow('guest', 'view foo');
    }

    /**
     * Test Orchestra\Authorization\Authorization::__call() method when execution is
     * not supported.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid keyword [add_foos]
     */
    public function testCallMagicMethodUsingMockOneThrowsExceptionForInvalidExecution()
    {
        $this->stub->addFoos('boss');
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

        $this->assertInstanceOf('\Orchestra\Contracts\Memory\Provider', $memory->getValue($stub));
        $this->assertInstanceOf('\Orchestra\Authorization\Fluent', $roles->getValue($stub));

        $this->assertTrue($stub->roles()->has('guest'));
        $this->assertTrue($stub->roles()->has('admin'));
        $this->assertTrue($stub->hasRole('guest'));
        $this->assertTrue($stub->hasRole('admin'));
        $this->assertEquals(['guest', 'admin'], $roles->getValue($stub)->get());
        $this->assertEquals(['guest', 'admin'], $stub->roles()->get());

        $this->assertInstanceOf('\Orchestra\Authorization\Fluent', $actions->getValue($stub));

        $this->assertTrue($stub->actions()->has('manage-user'));
        $this->assertTrue($stub->actions()->has('manage'));
        $this->assertTrue($stub->hasAction('manage-user'));
        $this->assertTrue($stub->hasAction('manage'));
        $this->assertEquals(['manage-user', 'manage'], $actions->getValue($stub)->get());
        $this->assertEquals(['manage-user', 'manage'], $stub->actions()->get());
    }

    /**
     * Test adding duplicate roles and actions is properly handled.
     *
     * @test
     */
    public function testAddDuplicates()
    {
        $runtime = $this->getRuntimeMemoryProvider();
        $runtime->put('acl_foo', $this->memoryProvider());

        $stub    = new Authorization($this->app['auth'], 'foo', $runtime);
        $refl    = new \ReflectionObject($stub);
        $actions = $refl->getProperty('actions');
        $roles   = $refl->getProperty('roles');

        $actions->setAccessible(true);
        $roles->setAccessible(true);

        $stub->roles()->add('admin');
        $stub->roles()->attach(['admin']);
        $stub->addRole('admin');
        $stub->addRoles(['admin', 'moderator']);
        $stub->removeRoles(['moderator']);

        $stub->actions()->add('manage');
        $stub->actions()->attach(['manage']);
        $stub->addAction('manage');
        $stub->addActions(['manage']);

        $this->assertEquals(['guest', 'admin'], $roles->getValue($stub)->get());
        $this->assertEquals(['guest', 'admin'], $stub->roles()->get());

        $this->assertEquals(['manage-user', 'manage'], $actions->getValue($stub)->get());
        $this->assertEquals(['manage-user', 'manage'], $stub->actions()->get());
    }
}
