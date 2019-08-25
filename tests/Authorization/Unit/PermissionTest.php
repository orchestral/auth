<?php

namespace Orchestra\Authorization\Tests\Unit;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Collection;
use Orchestra\Authorization\Permission;

class PermissionTest extends TestCase
{
    use Permission;

    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function it_can_interact_with_user()
    {
        $this->assertNull($this->userRoles);

        $user = m::mock('\Orchestra\Contracts\Authorization\Authorizable, \Illuminate\Contracts\Support\Arrayable');
        $roles = new Collection(['Administrator']);

        $user->shouldReceive('getRoles')->once()->andReturn($roles);

        $this->assertEquals($this, $this->setUser($user));
        $this->assertEquals($roles, $this->getUserRoles());

        $this->revokeUser();

        $this->assertNull($this->userRoles);
    }
}
