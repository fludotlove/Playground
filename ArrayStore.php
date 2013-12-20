<?php
/**
 * ArrayStore class definition.
 * 
 * @author Nathan Marshall <nathanm@studioskylab.com>
 * @copyright 2013, Nathan Marshall
 */

/**
 * Array store with dot-notation access.
 * 
 * @author Nathan Marshall <nathanm@studioskylab.com>
 */
class ArrayStore implements Countable, IteratorAggregate, JsonSerializable, Serializable
{

	/**
	 * Items in the store.
	 * 
	 * @var array $items
	 */
	protected $items = [];

	/**
	 * Create a store instance.
	 * 
	 * @param array $items Items to add to the store.
     * @return self
	 */
	public function __construct(array $items = null)
	{
		if (is_array($items)) {
			$this->items = $items;
		}
	}

    /**
     * Get any items which pass a given truth test.
     * 
     * @param callback $callback Callback to run.
     * @param mixed $default Value to return if all items fail.
     * @return mixed
     */
    public function all($callback, $default = null)
    {
        $return = [];

        foreach ($this->items as $key => $value) {
            if (call_user_func($callback, $key, $value)) {
                $return[] = $value;
            }
        }

        return !empty($return) ? $return : $default;
    }

	/**
	 * Count the number of items in the store.
     * 
	 * @param string|null $key Item to count.
     * @return int Number of items.
	 */
	public function count($key = null)
	{
		if (null !== $key && is_array($this->items[$key])) {
			return count($this->items[$key]);
		}

		return count($this->items);
	}

    /**
     * Get all store items except for a specified array of items.
     * 
     * @param array $keys Array of items.
     * @param string $key Item to use.
     * @return array Array of returned items.
     */
    public function except(array $keys, $key = null)
    {
        $items = (null !== $key) ? $this->get($key) : $this->items;

        return array_diff_key((array)$items, array_flip($keys));
    }

    /**
     * Get the first item to pass a given truth test.
     * 
     * @param callback $callback Callback to run.
     * @param mixed $default Value to return if all items fail.
     * @return mixed
     */
    public function first(Closure $callback, $default = null)
    {
        foreach ($this->items as $key => $value) {
            if (call_user_func($callback, $key, $value)) {
                return $value;
            }
        }

        return $default;
    }

    /**
     * Remove an item from the store.
     * 
     * @param string $key Item to remove.
     * @return self
     */
    public function forget($key)
    {
    	if ($this->_uses($key)) {
    		$this->_forget($this->items, $key);
    	} else {
    		if (array_key_exists($key, $this->items)) {
    			unset($this->items[$key]);
    		}
    	}

    	return $this;
    }

    /**
     * Get an item from the store.
     * 
     * @param string $key Item to get.
     * @param mixed $default Default value to return if item doesn't exist.
     * @return mixed
     */
    public function get($key = null, $default = null)
    {
        if (null === $key) {
            return $this->items;
        }

		if ($this->_uses($key)) {
			return $this->_get($this->items, $key, $default);
		} else {
			if (array_key_exists($key, $this->items)) {
				return $this->items[$key];
			}
		}

    	return $default;
    }

    /**
     * Return an array of items in the store.
     * 
     * @return ArrayIterator
     */
	public function getIterator()
	{
		return new ArrayIterator($this->items);
	}

    /**
     * Determine if the store has an item.
     * 
     * @param string $key Item to check for.
     * @return bool
     */
	public function has($key)
	{
		if ($this->_uses($key)) {
			return $this->_has($this->items, $key);
		} else {
			return array_key_exists($key, $this->items);
		}
	}

    /**
     * Return an array of store items to serialize as JSON.
     * 
     * @return array
     */
    public function jsonSerialize() 
    {
        return $this->items;
    }

    /**
     * Merge an array or instance of ArrayStore into this store.
     * 
     * @param array|ArrayStore $array Array or ArrayStore to merge.
     * @param bool $overwrite Overwrite existing keys if duplicated?
     * @return self
     * 
     * @throws InvalidArgumentException When not passed an array or ArrayStore instance.
     */
    public function merge($array, $overwrite = true)
    {
        if (!is_array($array) && !$array instanceof ArrayStore) {
            throw new InvalidArgumentException('Merge expects an array or ArrayStore instance.');
        }

        if ($array instanceof ArrayStore) {
            $array = $array->get();
        }

        if (true !== $overwrite) {
            $this->items = array_merge($array, $this->items);
        } else {
            $this->items = array_merge($this->items, $array);
        }

        return $this;
    }

    /**
     * Get only store items specified in an array of items.
     * 
     * @param array $keys Array of items.
     * @param string $key Item to use.
     * @return array Array of returned items.
     */
    public function only(array $keys, $key = null)
    {
        $items = (null !== $key) ? $this->get($key) : $this->items;

        return array_intersect_key((array)$items, array_flip($keys));
    }

    /**
     * Pluck an array of item values.
     * 
     * @param string $key Key to pluck.
     * @return array Array of returned item values.
     */
    public function pluck($key)
    {
        return array_map(function($value) use ($key) {
            return $this->_uses($key) ? $this->_get($value, $key) : $value[$key];
        }, $this->items);
    }

    /**
     * Push an item into a key. 
     * 
     * @param string $key Item to push to.
     * @param mixed $value Item value.
     * @return self
     */
    public function push($key, $value)
    {
        if (!$this->_has($this->items, $key)) {
            $this->set($key, $value);
        } else {
            $item = $this->get($key);

            if (is_array($item)) {
                $item[] = $value;

                $this->set($key, $item);
            } else {
                $this->set($key, [$item, $value]);
            }
        }

        return $this;
    }

    /**
     * Serialize the items in the store.
     * 
     * @return string
     */
    public function serialize() 
    {
        return serialize($this->items);
    }

    /**
     * Add or update an item in the store.
     * 
     * @param string $key Item to set or update.
     * @param mixed $value Item value.
     * @param bool $overwrite Overwrite the existing key?
     * @return self
     */
    public function set($key, $value, $overwrite = true)
    {
        if ($this->has($key) && true !== $overwrite) {
            return $this;
        }

    	if ($this->_uses($key)) {
    		$this->_set($this->items, $key, $value);
    	} else {
    		$this->items[$key] = $value;
    	}

    	return $this;
    }

    /**
     * Get the ArrayStore as a plain array.
     *
     * @return array Array of items.
     */
    public function toArray()
    {
        return $this->items;
    }

    /**
     * Set the store items from a serialized string (overwriting all previous items).
     * 
     * @param string $items Serialized string of items.
     * @return self
     */
    public function unserialize($items) 
    {
        $this->items = unserialize($items);

        return $this;
    }

    /**
     * Remove an item from the store using dot-notation.
     * 
     * @param array $array Array of items.
     * @param string $key Item to remove.
     * @return void
     */
    protected function _forget(array &$array, $key)
    {
        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (!isset($array[$key]) || !is_array($array[$key])) {
                return;
            }

            $array =& $array[$key];
        }

        unset($array[array_shift($keys)]);
    }

    /**
     * Get an item from the store using dot-notation.
     * 
     * @param array $array Array of items.
     * @param string $key Item to get.
     * @param mixed $default Default value to return if item doesn't exist.
     * @return mixed
     */
    protected function _get(array $array, $key, $default = null)
    {
        $keys = explode('.', $key);

        foreach ($keys as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return $default;
            }

            $array = $array[$segment];
        }

        return $array;
    }

    /**
     * Determine if the store has an item using dot-notation.
     * 
     * @param array $array Array of items.
     * @param string $key Item to check for.
     * @return bool
     */
    protected function _has(array $array, $key)
    {
        return null !== $this->_get($array, $key, null);
    }

    /**
     * Add or update an item in the store using dot-notation.
     * 
     * @param array $array Array of items.
     * @param string $key Item to set or update.
     * @param mixed $value Item value.
     * @return void
     */
    protected function _set(array &$array, $key, $value)
    {
        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = [];
            }

            $array =& $array[$key];
        }

        $array[array_shift($keys)] = $value;
    }

    /**
     * Determine if a key uses dot-notation.
     * 
     * @param string $key Key.
     * @return bool
     */
    protected function _uses($key)
    {
    	return strpos($key, '.') === false ? false : true;
    }

}
