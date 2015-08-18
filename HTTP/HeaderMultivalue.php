<?php
namespace CodeIgniter\HTTP;

abstract class HeaderMultivalue extends HeaderSimple
{
	protected $valueArray;

	public function __construct($value)
	{
		$this->valueString = $value;

		if (\is_string($value))
		{
			$value = (\strpos($value, ',') !== false)
				? \array_map('\trim', \explode(',', $value))
				: (array) $value;
		}
		elseif ( ! \is_array($value))
		{
			throw new \InvalidArgumentException('Input must be either an array or a string comma-separated list');
		}

		$this->valueArray = $value;
	}

	public function addValue($value)
	{
		$this->values[] = $value;
		$this->value .= ','.$value;
		return $this;
	}

	public function getValues()
	{
		return $this->valueArray;
	}
}
