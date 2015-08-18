<?php
namespace CodeIgniter\HTTP;

interface HeaderInterface
{
	public function __construct($value);
	public function getName();
	public function getValue();
	public function __toString();
}
