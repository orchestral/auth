<?php namespace Orchestra\Auth\Tests\Acl;

use Orchestra\Auth\Acl\Fluent;

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
		$this->stub = new Fluent('stub');
		$this->stub->attach(array('Hello World'));
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($this->stub);
	}

	/**
	 * Test instanceof stub.
	 *
	 * @test
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
	 */
	public function testAddMethod()
	{
		$stub = new Fluent('foo');

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
	 */
	public function testAddMethodNullThrownException()
	{
		$stub = new Fluent('foo');

		$stub->add(null);
	}

	/**
	 * Test Orchestra\Auth\Acl\Fluent::attach() method.
	 *
	 * @test
	 */
	public function testAttachMethod()
	{
		$stub = new Fluent('foo');

		$stub->attach(array('foo', 'foobar'));

		$refl = new \ReflectionObject($stub);
		$collections = $refl->getProperty('collections');
		$collections->setAccessible(true);

		$this->assertEquals(array('foo', 'foobar'), $collections->getValue($stub));
		$this->assertEquals(array('foo', 'foobar'), $stub->get());
	}

	/**
	 * Test Orchestra\Auth\Acl\Fluent::fill() method.
	 *
	 * @test
	 */
	public function testFillMethod()
	{
		$stub = new Fluent('foo');

		$stub->fill(array('foo', 'foobar'));

		$refl = new \ReflectionObject($stub);
		$collections = $refl->getProperty('collections');
		$collections->setAccessible(true);

		$this->assertEquals(array('foo', 'foobar'), $collections->getValue($stub));
		$this->assertEquals(array('foo', 'foobar'), $stub->get());
	}

	/**
	 * Test Orchestra\Auth\Acl\Fluent::attach() method null throw an exception.
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testAttachMethodNullThrownException()
	{
		$stub = new Fluent('foo');

		$stub->attach(array(null));
	}

	/**
	 * Test Orchestra\Auth\Acl\Fluent::has() method.
	 *
	 * @test
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
	 */
	public function testRenameMethod()
	{
		$stub = new Fluent('foo');

		$stub->attach(array('foo', 'foobar'));

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
	 */
	public function testSearchMethod()
	{
		$stub = new Fluent('foo');

		$stub->attach(array('foo', 'foobar'));

		$this->assertEquals(0, $stub->search('foo'));
		$this->assertEquals(1, $stub->search('foobar'));
		$this->assertNull($stub->search('laravel'));
	}

	/**
	 * Test Orchestra\Auth\Acl\Fluent::exist() method.
	 *
	 * @test
	 */
	public function testExistMethod()
	{
		$stub = new Fluent('foo');

		$stub->attach(array('foo', 'foobar'));

		$this->assertTrue($stub->exist(0));
		$this->assertTrue($stub->exist(1));
		$this->assertFalse($stub->exist(3));
	}

	/**
	 * Test Orchestra\Auth\Acl\Fluent::remove() method.
	 *
	 * @test
	 */
	public function testRemoveMethod()
	{
		$stub = new Fluent('foo');

		$stub->attach(array('foo', 'foobar'));

		$this->assertEquals(array('foo', 'foobar'), $stub->get());

		$stub->remove('foo');

		$this->assertFalse($stub->exist(0));
		$this->assertTrue($stub->exist(1));
		$this->assertEquals(array(1 => 'foobar'), $stub->get());

		$stub->attach(array('foo'));

		$this->assertEquals(array(1 => 'foobar', 2 => 'foo'), $stub->get());

		$stub->remove('foo');

		$this->assertFalse($stub->exist(0));
		$this->assertTrue($stub->exist(1));
		$this->assertFalse($stub->exist(2));
		$this->assertEquals(array(1 => 'foobar'), $stub->get());
	
		$this->assertFalse($stub->remove('hello'));
	}

	/**
	 * Test Orchestra\Auth\Acl\Fluent::detach() method.
	 *
	 * @test
	 */
	public function testDetachMethod()
	{
		$stub = new Fluent('foo');

		$stub->attach(array('foo', 'foobar'));

		$this->assertEquals(array('foo', 'foobar'), $stub->get());

		$stub->detach(array('foo'));

		$this->assertFalse($stub->exist(0));
		$this->assertTrue($stub->exist(1));
		$this->assertEquals(array(1 => 'foobar'), $stub->get());

		$stub->attach(array('foo'));

		$this->assertEquals(array(1 => 'foobar', 2 => 'foo'), $stub->get());

		$stub->detach(array('foo'));

		$this->assertFalse($stub->exist(0));
		$this->assertTrue($stub->exist(1));
		$this->assertFalse($stub->exist(2));
		$this->assertEquals(array(1 => 'foobar'), $stub->get());	
	}

	/**
	 * Test Orchestra\Auth\Acl\Fluent::remove() method null throw an exception.
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testRemoveMethodNullThrownException()
	{
		with(new Fluent('foo'))->remove(null);
	}

	/**
	 * Test Orchestra\Auth\Acl\Fluent::filter() method.
	 *
	 * @test
	 */
	public function testFilterMethod()
	{
		$stub = new Fluent('foo');
		$stub->attach(array('foo', 'foobar'));

		$this->assertEquals(array('foo', 'foobar'), $stub->filter('*'));
		$this->assertEquals(array(1 => 'foobar'), $stub->filter('!foo'));
		$this->assertEquals(array('hello-world'), $stub->filter('hello-world'));
	}
}
