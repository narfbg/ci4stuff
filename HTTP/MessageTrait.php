<?php
namespace CodeIgniter\HTTP;

trait MessageTrait
{
	protected $protocolVersion;

	protected $headers;
	protected $body;

	protected $isComplete = false;

	public function setHeader($name, $value)
	{
		if ($this->isComplete)
		{
			throw new \RuntimeException(\get_class($this).' instance already finalized');
		}

		$this->headers[$name] = $value;
		return $this;
	}

	public function appendHeader($name, $value)
	{
		if ($this->isComplete)
		{
			throw new \RuntimeException(\get_class($this).' instance already finalized');
		}

		if ( ! \is_array($this->headers[$name]) && ! ($this->headers[$name] instanceof \ArrayAccess))
		{
			($this->headers[$name] instanceof HeaderInterface) && $name = $this->headers[$name]->getName();
			throw new \LogicException('Header "'.$name.'" does not support multiple values');
		}

		$this->headers[$name][] = $value;
		return $this;
	}

	public function removeHeader($name)
	{
		if ($this->isComplete)
		{
			throw new \RuntimeException(\get_class($this).' instance already finalized');
		}

		unset($this->headers[$name]);
		return $this;
	}

	public function getHeader($name)
	{
		return isset($this->headers[$name])
			? $this->headers[$name]
			: null;
	}

	public function getHeaders()
	{
		return $this->headers;
	}

	public function getProtocolVersion()
	{
		return $this->protocolVersion;
	}

	public function setBody(&$data)
	{
		if ($this->isComplete)
		{
			throw new \RuntimeException(\get_class($this).' instance already finalized');
		}

		$this->body = $data;
		return $this;
	}

	public function getBody()
	{
		return $this->body;
	}

	public function complete()
	{
		$this->isComplete = true;
		return $this;
	}
}
