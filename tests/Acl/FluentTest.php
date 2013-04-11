<?php namespace Orchestra\Auth\Tests\Acl;

class FluentTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Stub instance.
	 * 
	 * @return Orchestra\Auth\Acl\Fluent
	 */
	private $stub = null;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$this->stub = new \Orchestra\Auth\Acl\Fluent('stub');
		$this->stub->fill(array(
			'Hello World'
		));
	}

	/**
	 * Test instanceof stub.
	 *
	 * @test
	 * @group support
	 */
	public function testInstanceOf()
	{
		$this->assertInstanceOf('\Orchestra\Auth\Acl\Fluent', $this->stub);

		$refl = new \ReflectionObject($this->stub);
		$name = $refl->getProperty('name');
		$name->setAccessible(true);

		$this->assertEquals('stub', $name->getValue($this->stub));
	}

	/**
	 * Test Orchestra\Auth\Acl\Fluent::add() method.
	 *
	 * @test
	 * @group support
	 */
	public function testAddMethod()
	{
		$stub = new \Orchestra\Auth\Acl\Fluent('foo');

		$stub->add('foo');
		$stub->add('foobar');

		$refl = new \ReflectionObject($stub);
		$collections = $refl->getProperty('collections');
		$collections->setAccessible(true);

		$this->assertEquals(array('foo', 'foobar'), $collections->getValue($stub));
		$this->assertEquals(array('foo', 'foobar'), $stub->get());
	}

	/**
	 * Test Orchestra\Auth\Acl\Fluent::add() method null throw an exception.
	 *
	 * @expectedException \InvalidArgumentException
	 * @group support
	 */
	public function testAddMethodNullThrownException()
	{
		$stub = new \Orchestra\Auth\Acl\Fluent('foo');

		$stub->add(null);
	}

	/**
	 * Test Orchestra\Auth\Acl\Fluent::fill() method.
	 *
	 * @test
	 * @group support
	 */
	public function testFillMethod()
	{
		$stub = new \Orchestra\Auth\Acl\Fluent('foo');

		$stub->fill(array('foo', 'foobar'));

		$refl = new \ReflectionObject($stub);
		$collections = $refl->getProperty('collections');
		$collections->setAccessible(true);

		$this->assertEquals(array('foo', 'foobar'), $collections->getValue($stub));
		$this->assertEquals(array('foo', 'foobar'), $stub->get());
	}

	/**
	 * Test Orchestra\Auth\Acl\Fluent::add() method null throw an exception.
	 *
	 * @expectedException \InvalidArgumentException
	 * @group support
	 */
	public function testFillMethodNullThrownException()
	{
		$stub = new \Orchestra\Auth\Acl\Fluent('foo');

		$stub->fill(array(null));
	}

	/**
	 * Test Orchestra\Auth\Acl\Fluent::has() method.
	 *
	 * @test
	 * @group support
	 */
	public function testHasMethod()
	{
		$this->assertTrue($this->stub->has('hello-world'));
		$this->assertFalse($this->stub->has('goodbye-world'));
	}

	/**
	 * Test Orchestra\Auth\Acl\Fluent::rename() method.
	 *
	 * @test
	 * @group support
	 */
	public function testRenameMethod()
	{
		$stub = new \Orchestra\Auth\Acl\Fluent('foo');

		$stub->fill(array('foo', 'foobar'));

		$stub->rename('foo', 'laravel');

		$refl = new \ReflectionObject($stub);
		$collections = $refl->getProperty('collections');
		$collections->setAccessible(true);

		$this->assertEquals(array('laravel', 'foobar'), $collections->getValue($stub));
		$this->assertEquals(array('laravel', 'foobar'), $stub->get());
	}

	/**
	 * Test Orchestra\Auth\Acl\Fluent::search() method.
	 *
	 * @test
	 * @group support
	 */
	public function testSearchMethod()
	{
		$stub = new \Orchestra\Auth\Acl\Fluent('foo');

		$stub->fill(array('foo', 'foobar'));

		$this->assertEquals(0, $stub->search('foo'));
		$this->assertEquals(1, $stub->search('foobar'));
		$this->assertTrue(is_null($stub->search('laravel')));
	}

	/**
	 * Test Orchestra\Auth\Acl\Fluent::exist() method.
	 *
	 * @test
	 * @group support
	 */
	public function testExistMethod()
	{
		$stub = new \Orchestra\Auth\Acl\Fluent('foo');

		$stub->fill(array('foo', 'foobar'));

		$this->assertTrue($stub->exist(0));
		$this->assertTrue($stub->exist(1));
		$this->assertFalse($stub->exist(3));
	}

	/**
	 * Test Orchestra\Auth\Acl\Fluent::remove() method.
	 *
	 * @test
	 * @group support
	 */
	public function testRemoveMethod()
	{
		$stub = new \Orchestra\Auth\Acl\Fluent('foo');

		$stub->fill(array('foo', 'foobar'));

		$this->assertEquals(array('foo', 'foobar'), $stub->get());

		$stub->remove('foo');

		$this->assertFalse($stub->exist(0));
		$this->assertTrue($stub->exist(1));
		$this->assertEquals(array(1 => 'foobar'), $stub->get());

		$stub->fill(array('foo'));

		$this->assertEquals(array(1 => 'foobar', 2 => 'foo'), $stub->get());

		$stub->remove('foo');

		$this->assertFalse($stub->exist(0));
		$this->assertTrue($stub->exist(1));
		$this->assertFalse($stub->exist(2));
		$this->assertEquals(array(1 => 'foobar'), $stub->get());
	
		$this->assertFalse($stub->remove('hello'));
	}

	/**
	 * Test Orchestra\Auth\Acl\Fluent::remove() method null throw an exception.
	 *
	 * @expectedException \InvalidArgumentException
	 * @group support
	 */
	public function testRemoveMethodNullThrownException()
	{
		$stub = new \Orchestra\Auth\Acl\Fluent('foo');

		$stub->remove(null);
	}

	/**
	 * Test Orchestra\Auth\Acl\Fluent::filter() method.
	 *
	 * @test
	 * @group support
	 */
	public function testFilterMethod()
	{
		$stub = new \Orchestra\Auth\Acl\Fluent('foo');
		$stub->fill(array('foo', 'foobar'));

		$this->assertEquals(array('foo', 'foobar'), $stub->filter('*'));
		$this->assertEquals(array(1 => 'foobar'), $stub->filter('!foo'));
		$this->assertEquals(array('hello-world'), $stub->filter('hello-world'));
	}
}