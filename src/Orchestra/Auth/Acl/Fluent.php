<?php namespace Orchestra\Auth\Acl;

use InvalidArgumentException;
use Illuminate\Support\Str;

class Fluent
{
    /**
     * Collection name.
     *
     * @var string
     */
    protected $name = null;

    /**
     * Collection of this instance.
     *
     * @var array
     */
    protected $collections = array();

    /**
     * Construct a new instance.
     *
     * @param  string   $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Get the collections.
     *
     * @return array
     */
    public function get()
    {
        return $this->collections;
    }

    /**
     * Determine whether a key exists in collection.
     *
     * @param  string   $key
     * @return boolean
     */
    public function has($key)
    {
        $key = strval($key);
        $key = trim(Str::slug($key, '-'));

        return ( ! empty($key) and in_array($key, $this->collections));
    }

    /**
     * Add multiple key to collection.
     *
     * @param  array   $keys
     * @return boolean
     */
    public function attach(array $keys)
    {
        foreach ($keys as $key) {
            $this->add($key);
        }

        return true;
    }

    /**
     * Add multiple key to collection.
     *
     * @param  array   $keys
     * @return boolean
     */
    public function fill(array $keys)
    {
        return $this->attach($keys);
    }

    /**
     * Add a key to collection.
     *
     * @param  string   $key
     * @return boolean
     */
    public function add($key)
    {
        if (is_null($key)) {
            throw new InvalidArgumentException("Can't add NULL {$this->name}.");
        }

        $key = trim(Str::slug($key, '-'));

        if ($this->has($key)) {
            return false;
        }

        array_push($this->collections, $key);

        return true;
    }

    /**
     * Rename a key from collection.
     *
     * @param  string   $from
     * @param  string   $to
     * @return boolean
     */
    public function rename($from, $to)
    {
        $from = trim(Str::slug($from, '-'));
        $to   = trim(Str::slug($to, '-'));

        if (is_null($key = $this->search($from))) {
            return false;
        }

        $this->collections[$key] = $to;

        return true;
    }

    /**
     * Remove a key from collection.
     *
     * @param  string   $key
     * @return boolean
     */
    public function remove($key)
    {
        if (is_null($key)) {
            throw new InvalidArgumentException("Can't add NULL {$this->name}.");
        }

        $key = trim(Str::slug($key, '-'));

        if (! is_null($id = $this->search($key))) {
            unset($this->collections[$id]);
            return true;
        }

        return false;
    }

    /**
     * Remove multiple key to collection.
     *
     * @param  array   $keys
     * @return boolean
     */
    public function detach(array $keys)
    {
        foreach ($keys as $key) {
            $this->remove($key);
        }

        return true;
    }

    /**
     * Get the ID from a key.
     *
     * @param  string   $key
     * @return integer
     */
    public function search($key)
    {
        $id = array_search($key, $this->collections);

        if (false === $id) {
            return null;
        }

        return $id;
    }

    /**
     * Check if an id is set in the collection.
     *
     * @param  integer  $id
     * @return bool
     */
    public function exist($id)
    {
        return isset($this->collections[$id]);
    }

    /**
     * Filter request.
     *
     * @param  string|array $request
     * @return array
     */
    public function filter($request)
    {
        if (is_array($request)) {
            return $request;
        } elseif ($request === '*') {
            $request = $this->get();
        } elseif ($request[0] === '!') {
            $request = array_diff($this->get(), array(substr($request, 1)));
        } elseif (! is_array($request)) {
            $request = array($request);
        }

        return $request;
    }
}
