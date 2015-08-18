<?php
namespace CodeIgniter\HTTP;

interface MessageInterface
{
	public function setHeader($name, $value);
	public function appendHeader($name, $value);
	public function removeHeader($name);

	public function getHeader($name);
	public function getHeaders();

	public function getStartLine();
	public function getProtocolVersion();

	public function setBody(&$data);
	public function getBody();
}
