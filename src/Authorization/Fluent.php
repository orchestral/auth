<?php

namespace Orchestra\Authorization;

use InvalidArgumentException;
use Orchestra\Support\Keyword;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model as Eloquent;

abstract class Fluent
{
    /**
     * Collection name.
     *
     * @var string
     */
    protected $name;

    /**
     * Cached keyword.
     *
     * @var array
     */
    protected $cachedKeyword = [];

    /**
     * Collection of this instance.
     *
     * @var array
     */
    protected $items = [];

    /**
     * Add a key to collection.
     *
     * @param  \Illuminate\Database\Eloquent\Model|string  $key
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function add($key): bool
    {
        if (\is_null($key)) {
            throw new InvalidArgumentException("Can't add NULL to {$this->name}.");
        }

        // Type-hint the attribute value of an Eloquent result, if it was
        // given instead of a string.
        if ($key instanceof Eloquent) {
            $key = $key->getAttribute('name');
        }

        $keyword = $this->getKeyword($key);

        if ($this->has($keyword)) {
            return false;
        }

        \array_push($this->items, $keyword->getSlug());

        return true;
    }

    /**
     * Add multiple key to collection.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable|array  $keys
     *
     * @return bool
     */
    public function attach($keys): bool
    {
        if ($keys instanceof Arrayable) {
            $keys = $keys->toArray();
        }

        foreach ($keys as $key) {
            $this->add($key);
        }

        return true;
    }

    /**
     * Remove multiple key to collection.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable|array  $keys
     *
     * @return bool
     */
    public function detach($keys): bool
    {
        if ($keys instanceof Arrayable) {
            $keys = $keys->toArray();
        }

        foreach ($keys as $key) {
            $this->remove($key);
        }

        return true;
    }

    /**
     * Check if an id is set in the collection.
     *
     * @param  \Orchestra\Support\Keyword|string  $id
     *
     * @return bool
     */
    public function exists($id): bool
    {
        return $this->getKeyword($id)->hasIn($this->items);
    }

    /**
     * Filter request.
     *
     * @param  string|array  $request
     *
     * @return array
     */
    public function filter($request): array
    {
        if (\is_array($request)) {
            return $request;
        } elseif ($request === '*') {
            return $this->get();
        } elseif ($request[0] === '!') {
            return \array_diff($this->get(), [\substr($request, 1)]);
        }

        return [$request];
    }

    /**
     * Find collection key from a name.
     *
     * @param  \Orchestra\Support\Keyword|string  $name
     *
     * @return int|null
     */
    public function findKey($name): ?int
    {
        $keyword = $this->getKeyword($name);

        if (! (\is_numeric($name) && $keyword->hasIn($this->items))) {
            return (string) $keyword->searchIn($this->items);
        }

        return $name;
    }

    /**
     * Get the items.
     *
     * @return array
     */
    public function get(): array
    {
        return $this->items;
    }

    /**
     * Determine whether a key exists in collection.
     *
     * @param  \Orchestra\Support\Keyword|string  $key
     *
     * @return bool
     */
    public function has($key): bool
    {
        $key = $this->getKeyword($key)->getSlug();

        return ! empty($key) && \in_array($key, $this->items);
    }

    /**
     * Remove a key from collection.
     *
     * @param  \Orchestra\Support\Keyword|string|null  $key
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function remove($key): bool
    {
        if (\is_null($key)) {
            throw new InvalidArgumentException("Can't remove NULL from {$this->name}.");
        }

        if (! \is_null($id = $this->search($key))) {
            unset($this->items[$id]);

            return true;
        }

        return false;
    }

    /**
     * Rename a key from collection.
     *
     * @param  \Orchestra\Support\Keyword|string  $from
     * @param  \Orchestra\Support\Keyword|string  $to
     *
     * @return bool
     */
    public function rename($from, $to): bool
    {
        $key = $this->search($from);

        if (\is_null($key)) {
            return false;
        }

        $this->items[$key] = $this->getKeyword($to)->getSlug();

        return true;
    }

    /**
     * Get the ID from a key.
     *
     * @param  \Orchestra\Support\Keyword|string  $key
     *
     * @return int|null
     */
    public function search($key): ?int
    {
        $id = $this->getKeyword($key)->searchIn($this->items);

        if (false === $id) {
            return null;
        }

        return $id;
    }

    /**
     * Get keyword instance.
     *
     * @param  \Orchestra\Support\Keyword|string  $key
     *
     * @return \Orchestra\Support\Keyword
     */
    protected function getKeyword($key): Keyword
    {
        if ($key instanceof Keyword) {
            return $key;
        }

        if (! isset($this->cachedKeyword[$key])) {
            $this->cachedKeyword[$key] = Keyword::make($key);
        }

        return $this->cachedKeyword[$key];
    }
}
