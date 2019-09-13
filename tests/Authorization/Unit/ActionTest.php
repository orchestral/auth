<?php

namespace Orchestra\Authorization\Tests\Unit;

use Illuminate\Support\Collection;
use Mockery as m;
use Orchestra\Authorization\Action;
use PHPUnit\Framework\TestCase;

class ActionTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function it_can_add_actions()
    {
        $stub = new Action();
        $model = m::mock('\Illuminate\Database\Eloquent\Model');

        $model->shouldReceive('getAttribute')->once()->with('name')->andReturn('eloquent');

        $stub->add('foo');
        $stub->add('foobar');
        $stub->add($model);

        $expected = ['foo', 'foobar', 'eloquent'];
        $this->assertEquals($expected, $stub->get());
    }

    /** @test */
    public function it_cant_add_null_as_action()
    {
        $this->expectException('InvalidArgumentException');

        $stub = new Action();

        $stub->add(null);
    }

    /** @test */
    public function it_can_attach_actions()
    {
        $stub = new Action();

        $stub->attach(['foo', 'foobar']);

        $this->assertEquals(['foo', 'foobar'], $stub->get());
    }

    /** @test */
    public function it_can_attach_arrayable_to_actions()
    {
        $stub = new Action();

        $stub->attach(new Collection(['foo', 'foobar']));

        $this->assertEquals(['foo', 'foobar'], $stub->get());
    }

    /** @test */
    public function it_throws_exception_when_attaching_null_to_actions()
    {
        $this->expectException('InvalidArgumentException');

        $stub = new Action();

        $stub->attach([null]);
    }

    /** @test */
    public function it_can_rename_actions()
    {
        $stub = new Action();

        $stub->attach(['foo', 'foobar']);

        $stub->rename('foo', 'laravel');

        $this->assertEquals(['laravel', 'foobar'], $stub->get());

        $this->assertFalse($stub->rename('foo', 'hello'));
    }

    /** @test */
    public function it_can_search_actions()
    {
        $stub = new Action();

        $stub->attach(['foo', 'foobar']);

        $this->assertEquals(0, $stub->search('foo'));
        $this->assertEquals(1, $stub->search('foobar'));
        $this->assertNull($stub->search('laravel'));
    }

    /** @test */
    public function it_can_check_if_action_exists()
    {
        $stub = new Action();

        $stub->attach(['foo', 'foobar']);

        $this->assertTrue($stub->exists(0));
        $this->assertTrue($stub->exists(1));
        $this->assertFalse($stub->exists(3));
    }

    /** @test */
    public function it_can_remove_actions()
    {
        $stub = new Action();

        $stub->attach(['foo', 'foobar']);

        $this->assertEquals(['foo', 'foobar'], $stub->get());

        $stub->remove('foo');

        $this->assertFalse($stub->exists(0));
        $this->assertTrue($stub->exists(1));
        $this->assertEquals([1 => 'foobar'], $stub->get());

        $stub->attach(['foo']);

        $this->assertEquals([1 => 'foobar', 2 => 'foo'], $stub->get());

        $stub->remove('foo');

        $this->assertFalse($stub->exists(0));
        $this->assertTrue($stub->exists(1));
        $this->assertFalse($stub->exists(2));
        $this->assertEquals([1 => 'foobar'], $stub->get());

        $this->assertFalse($stub->remove('hello'));
    }

    /** @test */
    public function it_can_detach_actions()
    {
        $stub = new Action();

        $stub->attach(['foo', 'foobar']);

        $this->assertEquals(['foo', 'foobar'], $stub->get());

        $stub->detach(['foo']);

        $this->assertFalse($stub->exists(0));
        $this->assertTrue($stub->exists(1));
        $this->assertEquals([1 => 'foobar'], $stub->get());

        $stub->attach(['foo']);

        $this->assertEquals([1 => 'foobar', 2 => 'foo'], $stub->get());

        $stub->detach(['foo']);

        $this->assertFalse($stub->exists(0));
        $this->assertTrue($stub->exists(1));
        $this->assertFalse($stub->exists(2));
        $this->assertEquals([1 => 'foobar'], $stub->get());
    }

    /** @test */
    public function it_can_detach_actions_using_arrayable()
    {
        $stub = new Action();

        $stub->attach(['foo', 'foobar']);

        $this->assertEquals(['foo', 'foobar'], $stub->get());

        $stub->detach(new Collection(['foo']));

        $this->assertFalse($stub->exists(0));
        $this->assertTrue($stub->exists(1));
        $this->assertEquals([1 => 'foobar'], $stub->get());

        $stub->attach(['foo']);

        $this->assertEquals([1 => 'foobar', 2 => 'foo'], $stub->get());

        $stub->detach(new Collection(['foo']));

        $this->assertFalse($stub->exists(0));
        $this->assertTrue($stub->exists(1));
        $this->assertFalse($stub->exists(2));
        $this->assertEquals([1 => 'foobar'], $stub->get());
    }

    /** @test */
    public function it_throws_exception_when_removing_null()
    {
        $this->expectException('InvalidArgumentException');

        (new Action())->remove(null);
    }

    /** @test */
    public function it_can_filter_actions()
    {
        $stub = new Action();
        $stub->attach(['foo', 'foobar']);

        $this->assertEquals(['foo', 'foobar'], $stub->filter('*'));
        $this->assertEquals([1 => 'foobar'], $stub->filter('!foo'));
        $this->assertEquals(['hello-world'], $stub->filter('hello-world'));
    }
}
