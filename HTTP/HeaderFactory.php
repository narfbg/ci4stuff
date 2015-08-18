<?php
namespace CodeIgniter\HTTP;

class HeaderFactory
{
	protected static $classMap = [];

	public static function create($name, $value)
	{
		if (isset(static::$classMap[\strtolower($name)]))
		{
			$class = static::$classMap[\strtolower($name)];
			return new $class($value);
		}

		$name  = \ucfirst(\strtr(\trim($name), '-', ' '));
		$class = __NAMESPACE__.'\\Headers\\'.\strtr($name, ' ', '_');
		$name  = \strtr($name, ' ', '-');

		if (\class_exists($class))
		{
			if ( ! \is_subclass_of($class, __NAMESPACE__.'\\HeaderInterface'))
			{
				throw new \RuntimeException($class.' does not implement '.__NAMESPACE__.'\\HeaderInterface');
			}

			static::$classMap[\strtolower($name)] = $class;
			return new $class($value);
		}

		$class = __NAMESPACE__.'\\Headers\\Unknown';
		return new $class($value, $name);
	}

	public static function setHeaderClass($name, $class)
	{
		if (\class_exists($class))
		{
			throw new \InvalidArgumentException($class.' is undefined');
		}
		elseif (\is_subclass_of($class, __NAMESPACE__.'\\HeaderInterface'))
		{
			throw new \DomainException($class.' does not implement '.__NAMESPACE__.'\\HeaderInterface');
		}
		elseif ( ! preg_match('/^[a-z][a-z0-9]*(\-[a-z][a-z0-9]*)*$/i', $name))
		{
			throw new \DomainException($name.' is not a valid header name');
		}

		static::$classMap[\strtolower($name)] = $class;
	}
}
