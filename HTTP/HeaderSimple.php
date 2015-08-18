<?php
namespace CodeIgniter\HTTP;

abstract class HeaderSimple implements HeaderInterface
{
	protected $valueString;

	public function __construct($value)
	{
		$this->valueString = $value;
	}

	public function getName()
	{
		return \str_replace('_', '-', \get_class($this));
	}

	public function getValue()
	{
		return $this->valueString;
	}

	public function __toString()
	{
		return $this->getName().': '.$this->valueString;
	}
}
