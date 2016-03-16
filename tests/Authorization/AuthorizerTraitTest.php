<?php namespace Orchestra\Authorization\TestCase;

use Mockery as m;
use Illuminate\Support\Fluent;
use Orchestra\Authorization\Authorizer;

class AuthorizerTraitTest extends \PHPUnit_Framework_TestCase
{
    use Authorizer;

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    public function testGetterAndSetterForUser()
    {
        $this->assertNull($this->userRoles);

        $user = m::mock('\Orchestra\Contracts\Authorization\Authorizable, \Illuminate\Contracts\Support\Arrayable');
        $roles = [
            'Administrator',
        ];

        $user->shouldReceive('getRoles')->once()->andReturn(new Fluent($roles));

        $this->assertEquals($this, $this->setUser($user));
        $this->assertEquals($roles, $this->getUserRoles());

        $this->revokeUser();

        $this->assertNull($this->userRoles);
    }
}
