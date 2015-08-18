<?php
namespace CodeIgniter\HTTP;

interface RequestInterface extends MessageInterface
{
	public function __construct($method, $uri, $protocolVersion);
	public function getMethod();
	public function getURI();

}
