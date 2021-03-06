<?php

namespace Orchestra\Auth\Tests\Unit;

use Illuminate\Auth\Events\Authenticated;
use Illuminate\Support\Collection;
use Mockery as m;
use Orchestra\Auth\SessionGuard;
use PHPUnit\Framework\TestCase;

class SessionGuardTest extends TestCase
{
    /**
     * Provider instance.
     *
     * @var \Illuminate\Contracts\Auth\UserProvider
     */
    private $provider;

    /**
     * Session instance.
     *
     * @var \Illuminate\Session\Store
     */
    private $session;

    /**
     * Event dispatcher instance.
     *
     * @var \Illuminate\Event\Dispatcher
     */
    private $events;

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        $this->provider = m::mock('\Illuminate\Contracts\Auth\UserProvider');
        $this->session = m::mock('\Illuminate\Session\Store');
        $this->events = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $this->request = m::mock('\Illuminate\Http\Request');
    }

    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        unset($this->provider, $this->session, $this->events);

        m::close();
    }

    /**
     * Test Orchestra\Auth\SessionGuard::roles() method returning valid roles.
     *
     * @test
     */
    public function testRolesMethod()
    {
        $events = $this->events;

        $user = m::mock('\Illuminate\Contracts\Auth\Authenticatable');

        $user->shouldReceive('getAuthIdentifier')->once()->andReturn(1);

        $events->shouldReceive('dispatch')->once()
                ->with(m::type(Authenticated::class))->andReturnNull()
            ->shouldReceive('until')->once()
                ->with('orchestra.auth: roles', m::any())->andReturn(['admin', 'editor']);

        $stub = new SessionGuard('web', $this->provider, $this->session);
        $stub->setDispatcher($events);
        $stub->setUser($user);

        $expected = new Collection(['admin', 'editor']);
        $output = $stub->roles();

        $this->assertEquals($expected, $output);
    }

    /**
     * Test Orchestra\Auth\SessionGuard::roles() method when user is not logged in.
     *
     * @test
     */
    public function testRolesMethodWhenUserIsNotLoggedIn()
    {
        $events = $this->events;

        $user = m::mock('\Illuminate\Contracts\Auth\Authenticatable');

        $user->shouldReceive('getAuthIdentifier')->once()->andReturn(1);

        $events->shouldReceive('dispatch')->once()
                ->with(m::type(Authenticated::class))->andReturnNull()
            ->shouldReceive('until')->once()
                ->with('orchestra.auth: roles', m::any())->andReturnNull();

        $stub = new SessionGuard('web', $this->provider, $this->session);
        $stub->setDispatcher($events);
        $stub->setUser($user);

        $expected = new Collection(['Guest']);
        $output = $stub->roles();

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

        $user = m::mock('\Illuminate\Contracts\Auth\Authenticatable');

        $user->shouldReceive('getAuthIdentifier')->times(5)->andReturn(1);

        $events->shouldReceive('dispatch')->once()
                ->with(m::type(Authenticated::class))->andReturnNull()
            ->shouldReceive('until')->once()
                ->with('orchestra.auth: roles', m::any())->andReturn(['admin', 'editor']);

        $stub = new SessionGuard('web', $this->provider, $this->session);
        $stub->setDispatcher($events);
        $stub->setUser($user);

        $this->assertTrue($stub->is('admin'));
        $this->assertTrue($stub->is('editor'));
        $this->assertFalse($stub->is('user'));

        $this->assertTrue($stub->is(['admin', 'editor']));
        $this->assertFalse($stub->is(['admin', 'user']));
    }

    /**
     * Test Orchestra\Support\Auth::is() method when invalid roles is
     * returned.
     *
     * @test
     */
    public function testIsMethodWhenInvalidRolesIsReturned()
    {
        $events = $this->events;
        $user = m::mock('\Illuminate\Contracts\Auth\Authenticatable');

        $user->shouldReceive('getAuthIdentifier')->times(5)->andReturn(1);

        $events->shouldReceive('dispatch')->once()
                ->with(m::type(Authenticated::class))->andReturnNull()
            ->shouldReceive('until')
                ->with('orchestra.auth: roles', m::any())->once()
                ->andReturn('foo');

        $stub = new SessionGuard('web', $this->provider, $this->session);
        $stub->setDispatcher($events);
        $stub->setUser($user);

        $this->assertFalse($stub->is('admin'));
        $this->assertFalse($stub->is('editor'));
        $this->assertFalse($stub->is('user'));

        $this->assertFalse($stub->is(['admin', 'editor']));
        $this->assertFalse($stub->is(['admin', 'user']));
    }

    /**
     * Test Orchestra\Support\Auth::isAny() method returning valid roles.
     *
     * @test
     */
    public function testIsAnyMethod()
    {
        $events = $this->events;

        $user = m::mock('\Illuminate\Contracts\Auth\Authenticatable');

        $user->shouldReceive('getAuthIdentifier')->times(3)->andReturn(1);

        $events->shouldReceive('dispatch')->once()
                ->with(m::type(Authenticated::class))->andReturnNull()
            ->shouldReceive('until')->once()
                ->with('orchestra.auth: roles', m::any())
                ->andReturn(new Collection(['admin', 'editor']));

        $stub = new SessionGuard('web', $this->provider, $this->session);
        $stub->setDispatcher($events);
        $stub->setUser($user);

        $this->assertTrue($stub->isAny(['admin', 'user']));
        $this->assertTrue($stub->isAny(['user', 'editor']));
        $this->assertFalse($stub->isAny(['superadmin', 'user']));
    }

    /**
     * Test Orchestra\Support\Auth::isAny() method when invalid roles is
     * returned.
     *
     * @test
     */
    public function testIsAnyMethodWhenInvalidRolesIsReturned()
    {
        $events = $this->events;
        $user = m::mock('\Illuminate\Contracts\Auth\Authenticatable');

        $user->shouldReceive('getAuthIdentifier')->twice()->andReturn(1);

        $events->shouldReceive('dispatch')->once()
                ->with(m::type(Authenticated::class))->andReturnNull()
            ->shouldReceive('until')
                ->with('orchestra.auth: roles', m::any())->once()
                ->andReturn('foo');

        $stub = new SessionGuard('web', $this->provider, $this->session);
        $stub->setDispatcher($events);
        $stub->setUser($user);

        $this->assertFalse($stub->isAny(['admin', 'editor']));
        $this->assertFalse($stub->isAny(['admin', 'user']));
    }

    /**
     * Test Orchestra\Support\Auth::isNot() method returning valid roles.
     *
     * @test
     */
    public function testIsNotMethod()
    {
        $events = $this->events;

        $user = m::mock('\Illuminate\Contracts\Auth\Authenticatable');

        $user->shouldReceive('getAuthIdentifier')->times(5)->andReturn(1);

        $events->shouldReceive('dispatch')->once()
                ->with(m::type(Authenticated::class))->andReturnNull()
            ->shouldReceive('until')->once()
                ->with('orchestra.auth: roles', m::any())
                ->andReturn(['admin', 'editor']);

        $stub = new SessionGuard('web', $this->provider, $this->session);
        $stub->setDispatcher($events);
        $stub->setUser($user);

        $this->assertTrue($stub->isNot('superadmin'));
        $this->assertTrue($stub->isNot('user'));
        $this->assertFalse($stub->isNot('admin'));

        $this->assertTrue($stub->isNot(['superadmin', 'user']));
        $this->assertFalse($stub->isNot(['admin', 'editor']));
    }

    /**
     * Test Orchestra\Support\Auth::isNot() method when invalid roles is
     * returned.
     *
     * @test
     */
    public function testIsNotMethodWhenInvalidRolesIsReturned()
    {
        $events = $this->events;
        $user = m::mock('\Illuminate\Contracts\Auth\Authenticatable');

        $user->shouldReceive('getAuthIdentifier')->times(5)->andReturn(1);

        $events->shouldReceive('dispatch')->once()
                ->with(m::type(Authenticated::class))->andReturnNull()
            ->shouldReceive('until')
                ->with('orchestra.auth: roles', m::any())->once()
                ->andReturn('foo');

        $stub = new SessionGuard('web', $this->provider, $this->session);
        $stub->setDispatcher($events);
        $stub->setUser($user);

        $this->assertTrue($stub->isNot('admin'));
        $this->assertTrue($stub->isNot('editor'));
        $this->assertTrue($stub->isNot('user'));

        $this->assertTrue($stub->isNot(['admin', 'editor']));
        $this->assertTrue($stub->isNot(['admin', 'user']));
    }

    /**
     * Test Orchestra\Support\Auth::isAny() method returning valid roles.
     *
     * @test
     */
    public function testIsNotAnyMethod()
    {
        $events = $this->events;

        $user = m::mock('\Illuminate\Contracts\Auth\Authenticatable');

        $user->shouldReceive('getAuthIdentifier')->times(3)->andReturn(1);

        $events->shouldReceive('dispatch')->once()
                ->with(m::type(Authenticated::class))->andReturnNull()
            ->shouldReceive('until')->once()
                ->with('orchestra.auth: roles', m::any())
                ->andReturn(['admin', 'editor']);

        $stub = new SessionGuard('web', $this->provider, $this->session);
        $stub->setDispatcher($events);
        $stub->setUser($user);

        $this->assertTrue($stub->isNotAny(['administrator', 'user']));
        $this->assertFalse($stub->isNotAny(['user', 'editor']));
        $this->assertFalse($stub->isNotAny(['admin', 'editor']));
    }

    /**
     * Test Orchestra\Support\Auth::isNotAny() method when invalid roles is
     * returned.
     *
     * @test
     */
    public function testIsNotAnyMethodWhenInvalidRolesIsReturned()
    {
        $events = $this->events;
        $user = m::mock('\Illuminate\Contracts\Auth\Authenticatable');

        $user->shouldReceive('getAuthIdentifier')->twice()->andReturn(1);

        $events->shouldReceive('dispatch')->once()
                ->with(m::type(Authenticated::class))->andReturnNull()
            ->shouldReceive('until')
                ->with('orchestra.auth: roles', m::any())->once()
                ->andReturn('foo');

        $stub = new SessionGuard('web', $this->provider, $this->session);
        $stub->setDispatcher($events);
        $stub->setUser($user);

        $this->assertTrue($stub->isNotAny(['admin', 'editor']));
        $this->assertTrue($stub->isNotAny(['admin', 'user']));
    }
}
