<?php
namespace CodeIgniter;

class Autoload {

	protected static $map = [];

	public static function register()
	{
		static::add(__NAMESPACE__, __DIR__);
		return \spl_autoload_register(__NAMESPACE__.'\\Autoload::load', true, true);
	}

	public static function add($namespace, $directory) {

		if (\is_dir($directory)) $dir = \realpath($directory);
		else
		{
			// If the provided directory path wasn't absolute,
			// try to resolve it against the caller's path
			$dir = ($directory[0] !== '/')
				? \realpath(\dirname(\debug_backrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['file']).'/'.$directory)
				: false;
		}

		if ($dir === false)
		{
			throw new \RuntimeException('Unable to resolve '.$directory.' to a valid filesystem path');
		}

		static::$map[\trim($namespace, '\\').'\\'] = $dir.'/';
	}

	public static function remove($namespace)
	{
		$namespace = \trim($namespace, '\\').'\\';
		if (isset(static::$map[$namespace]))
		{
			unset(static::$map[$namespace]);
		}
	}

	protected static function load($class)
	{
		foreach (static::$map as $namespace => $directory)
		{
			if (\strpos($class, $namespace) === 0)
			{
				$filePath = $directory.\strtr(\substr($class, \strlen($namespace)), '\\', '/').'.php';
				\file_exists($filePath) && require_once $filePath;
			}
		}
	}
}
