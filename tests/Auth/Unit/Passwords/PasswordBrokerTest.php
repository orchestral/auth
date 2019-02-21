<?php

namespace Orchestra\Auth\TestCase\Unit\Passwords;

use Mockery as m;
use Illuminate\Support\Arr;
use PHPUnit\Framework\TestCase;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Orchestra\Auth\Passwords\PasswordBroker;

class PasswordBrokerTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        $app = new Container();
        $app['translator'] = $translator = m::mock('\Illuminate\Translation\Translator')->makePartial();
        $translator->shouldReceive('trans')->andReturn('foo');

        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($app);
    }

    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /**
     * Test Orchestra\Foundation\Reminders\PasswordBroker::remind() method.
     *
     * @test
     */
    public function testRemindMethod()
    {
        $stub = new PasswordBroker(
            $reminders = m::mock('\Illuminate\Auth\Passwords\TokenRepositoryInterface'),
            $user = m::mock('\Illuminate\Contracts\Auth\UserProvider, \Illuminate\Contracts\Auth\CanResetPassword'),
            'user'
        );

        $user->shouldReceive('retrieveByCredentials')->once()
            ->with(['username' => 'user-foo'])->andReturn($user);
        $reminders->shouldReceive('create')->once()->with($user)->andReturn('token');
        $user->shouldReceive('sendPasswordResetNotification')->once()->with('token', 'user');

        $this->assertEquals('passwords.sent', $stub->sendResetLink(['username' => 'user-foo']));
    }

    /**
     * Test Orchestra\Foundation\Reminders\PasswordBroker::remind() given
     * user is null.
     *
     * @test
     */
    public function testRemindMethodGivenUserIsNull()
    {
        $stub = new PasswordBroker(
            $reminders = m::mock('\Illuminate\Auth\Passwords\TokenRepositoryInterface'),
            $user = m::mock('\Illuminate\Contracts\Auth\UserProvider'),
            $mailer = m::mock('\Orchestra\Notifier\Handlers\Orchestra'),
            $view = 'foo'
        );

        $user->shouldReceive('retrieveByCredentials')->once()
            ->with(['username' => 'user-foo'])->andReturnNull();

        $this->assertEquals('passwords.user', $stub->sendResetLink(['username' => 'user-foo']));
    }

    /**
     * Test Orchestra\Foundation\Reminders\PasswordBroker::reset() method.
     *
     * @test
     */
    public function testResetMethod()
    {
        $stub = new PasswordBroker(
            $reminders = m::mock('\Illuminate\Auth\Passwords\TokenRepositoryInterface'),
            $user = m::mock('\Illuminate\Contracts\Auth\UserProvider'),
            $mailer = m::mock('\Orchestra\Notifier\Handlers\Orchestra'),
            $view = 'foo'
        );

        $reminderable = m::mock('\Illuminate\Contracts\Auth\CanResetPassword');

        $reminderable->shouldReceive('getEmailForPasswordReset')->andReturn('hello@orchestraplatform.com');

        $callback = function ($user, $pass) {
            return 'foo';
        };

        $credentials = [
            'username' => 'user-foo',
            'password' => 'qwerty123',
            'password_confirmation' => 'qwerty123',
            'token' => 'someuniquetokenkey',
        ];

        $user->shouldReceive('retrieveByCredentials')->once()
                ->with(Arr::except($credentials, ['token']))->andReturn($reminderable);
        $reminders->shouldReceive('exists')->once()->with($reminderable, 'someuniquetokenkey')->andReturn(true)
            ->shouldReceive('delete')->once()->with($reminderable)->andReturn(true);

        $this->assertEquals('passwords.reset', $stub->reset($credentials, $callback));
    }

    /**
     * Test Orchestra\Foundation\Reminders\PasswordBroker::reset() method
     * given user ins not \Illuminate\Auth\Passwords\RemindableInteface.
     *
     * @test
     */
    public function testResetMethodGivenUserIsNotRemindableInterface()
    {
        $stub = new PasswordBroker(
            $reminders = m::mock('\Illuminate\Auth\Passwords\TokenRepositoryInterface'),
            $user = m::mock('\Illuminate\Contracts\Auth\UserProvider'),
            $mailer = m::mock('\Orchestra\Notifier\Handlers\Orchestra'),
            $view = 'foo'
        );

        $callback = function ($user, $pass) {
            //
        };

        $credentials = [
            'username' => 'user-foo',
            'password' => 'qwerty',
            'password_confirmation' => 'qwerty',
            'token' => 'someuniquetokenkey',
        ];

        $user->shouldReceive('retrieveByCredentials')->once()
            ->with(Arr::except($credentials, ['token']))->andReturnNull();

        $this->assertEquals('passwords.user', $stub->reset($credentials, $callback));
    }

    /**
     * Test Orchestra\Foundation\Reminders\PasswordBroker::reset() method
     * given fail verify password.
     *
     * @test
     */
    public function testResetMethodGivenFailVerifyPassword()
    {
        $stub = new PasswordBroker(
            $reminders = m::mock('\Illuminate\Auth\Passwords\TokenRepositoryInterface'),
            $user = m::mock('\Illuminate\Contracts\Auth\UserProvider'),
            $mailer = m::mock('\Orchestra\Notifier\Handlers\Orchestra'),
            $view = 'foo'
        );

        $callback = function ($user, $pass) {
            //
        };

        $credentials = [
            'username' => 'user-foo',
            'password' => 'qwerty123',
            'password_confirmation' => 'qwerty123',
            'token' => 'someuniquetokenkey',
        ];

        $user->shouldReceive('retrieveByCredentials')->once()
                ->with(Arr::except($credentials, ['token']))
                ->andReturn($userReminderable = m::mock('\Illuminate\Contracts\Auth\CanResetPassword, \Orchestra\Contracts\Notification\Recipient'));
        $reminders->shouldReceive('exists')->once()->with($userReminderable, 'someuniquetokenkey')->andReturn(false);

        $this->assertEquals('passwords.token', $stub->reset($credentials, $callback));
    }

    /**
     * Test Orchestra\Foundation\Reminders\PasswordBroker::reset() method
     * given reminder not exist.
     *
     * @test
     */
    public function testResetMethodGivenReminderNotExist()
    {
        $stub = new PasswordBroker(
            $reminders = m::mock('\Illuminate\Auth\Passwords\TokenRepositoryInterface'),
            $user = m::mock('\Illuminate\Contracts\Auth\UserProvider'),
            $mailer = m::mock('\Orchestra\Notifier\Handlers\Orchestra'),
            $view = 'foo'
        );

        $callback = function ($user, $pass) {
            //
        };

        $credentials = [
            'username' => 'user-foo',
            'password' => 'qwerty123',
            'password_confirmation' => 'qwerty123',
            'token' => 'someuniquetokenkey',
        ];

        $user->shouldReceive('retrieveByCredentials')->once()
                ->with(Arr::except($credentials, ['token']))
                ->andReturn($userReminderable = m::mock('\Illuminate\Contracts\Auth\CanResetPassword, \Orchestra\Contracts\Notification\Recipient'));
        $reminders->shouldReceive('exists')->once()->with($userReminderable, 'someuniquetokenkey')->andReturn(false);

        $this->assertEquals('passwords.token', $stub->reset($credentials, $callback));
    }

    /**
     * Test Orchestra\Foundation\Reminders\PasswordBroker::getUser() method
     * throws exception.
     *
     * @test
     */
    public function testGetUserThrowsException()
    {
        $this->expectException('UnexpectedValueException');

        $stub = new PasswordBroker(
            $reminders = m::mock('\Illuminate\Auth\Passwords\TokenRepositoryInterface'),
            $user = m::mock('\Illuminate\Contracts\Auth\UserProvider'),
            $mailer = m::mock('\Orchestra\Notifier\Handlers\Orchestra'),
            $view = 'foo'
        );

        $user->shouldReceive('retrieveByCredentials')->once()->with([])->andReturn('foo');

        $stub->getUser([]);
    }
}
