<?php namespace Orchestra\Authorization;

use Illuminate\Support\Str;

class Keyword
{
    /**
     * Original value.
     *
     * @var string
     */
    protected $value = '';

    /**
     * Slug value.
     *
     * @var string|null
     */
    protected $slug;

    /**
     * Make a new Keyword Value Object.
     *
     * @param  \Orchestra\Authorization\Keyword|string  $value
     */
    public function __construct($value)
    {
        $this->value = $value;

        if (is_string($value)) {
            $this->slug = trim(Str::slug($value, '-'));
        }
    }

    /**
     * Make a new Keyword or return if it already is one.
     *
     * @param  \Orchestra\Authorization\Keyword|string  $value
     *
     * @return static
     */
    public static function make($value)
    {
        if ($value instanceof Keyword) {
            return $value;
        }

        return new static($value);
    }

    /**
     * Get keyword value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get slug string.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Search slug in given items and return the key.
     *
     * @param  array  $items
     *
     * @return mixed
     */
    public function searchIn(array $items = [])
    {
        if (is_null($slug = $this->slug)) {
            return array_search($this->value, $items);
        }

        return array_search($slug, $items);
    }

    /**
     * Search slug in given items and return if the key exist.
     *
     * @param  array  $items
     *
     * @return mixed
     */
    public function hasIn(array $items = [])
    {
        if (is_null($slug = $this->slug)) {
            return isset($items[$this->value]);
        }

        return isset($items[$slug]);
    }

    /**
     * Convert to string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getSlug();
    }
}
