<?php
namespace CodeIgniter\HTTP;

class HeaderCollection implements \ArrayAccess, \Iterator
{
	const FOR_REQUEST  = 0;
	const FOR_RESPONSE = 1;

	protected $messageType;

	protected $container = [];
	protected $position  = 0;

	public function __construct($type)
	{
		if ($type !== static::FOR_REQUEST && $type !== static::FOR_RESPONSE)
		{
			throw new \InvalidArgumentException('Invalid message type');
		}

		$this->messageType = $type;
	}

	public function offsetSet($key, $value)
	{
		if (empty($key) OR ! \is_string($key))
		{
			throw new \InvalidArgumentException("Invalid or missing header name");
		}

		$this->container[\strtolower($key)] = HeaderFactory::create($key, $value);
	}

	public function offsetGet($key)
	{
		$key = \strtolower($key);
		return isset($this->container[$key]) ? $this->container[$key] : null;
	}

	public function offsetExists($key)
	{
		return isset($this->container[\strtolower($key)]);
	}

	public function offsetUnset($key)
	{
		unset($this->container[\strtolower($key)]);
	}

	public function __toString()
	{
		return \implode("\n", $this->container);
	}

	public function current()
	{
		return \count($this->container)
			? $this->container[\array_keys($this->container)[$this->position]]
			: null;
	}

	public function key()
	{
		return \count($this->container)
			? \array_keys($this->container)[$this->position]
			: null;
	}

	public function next()
	{
		$this->position++;
	}

	public function rewind()
	{
		$this->position = 0;
	}

	public function valid()
	{
		return ($this->position < \count($this->container));
	}
}
