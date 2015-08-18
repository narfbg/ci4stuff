<?php
namespace CodeIgniter\HTTP\Headers;

class Unknown extends \CodeIgniter\HTTP\HeaderSimple
{
	protected $name;

	public function __construct($value, $name = 'Unknown')
	{
		parent::__construct($value);
		$this->name = $name;
	}

	public function getName()
	{
		return $this->name;
	}

	public function __toString()
	{
		return $this->name.': '.$this->valueString;
	}
}
