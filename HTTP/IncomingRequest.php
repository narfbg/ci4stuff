<?php
namespace CodeIgniter\HTTP;

class IncomingRequest extends Request
{
	// This will be overriden
	protected static $instance = true;

	public static function &getInstance()
	{
		if (static::$instance instanceof IncomingRequest)
		{
			return static::$instance;
		}

		if (isset($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME']))
		{
			if (\strpos($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME']) === 0)
			{
				$uri = (string) \substr($_SERVER['REQUEST_URI'], \strlen($_SERVER['SCRIPT_NAME']));
			}
			elseif (\strpos($_SERVER['REQUEST_URI'], \dirname($_SERVER['SCRIPT_NAME'])) === 0)
			{
				$uri = (string) \substr($_SERVER['REQUEST_URI'], \strlen(\dirname($_SERVER['SCRIPT_NAME'])));
			}
		}

		static::$instance = null; // Allow instantiation
		static::$instance = new static(
			isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'DUMMY',
			isset($uri) ? $uri : '/',
			isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'CLI'
		);

		foreach ($_SERVER as $key => &$value)
		{
			if (\substr($key, 0, 5) !== 'HTTP_')
			{
				continue;
			}

			$key = \strtr(\ucwords(\strtolower(\strtr(\substr($key, 5), '_', ' '))), ' ', '-');
			static::$instance->headers[$key] = $value;
		}

		static::$instance->isComplete = true;
		return static::$instance;
	}

	public function __construct($method, $uri, $version)
	{
		if (isset(static::$instance))
		{
			throw new \LogicException(\get_class($this).' can only be instantiated once');
		}

		$this->method = $method;
		$this->uri = $uri;
		$this->protocolVersion = $version;
		$this->headers = new HeaderCollection(HeaderCollection::FOR_REQUEST);
	}
}
