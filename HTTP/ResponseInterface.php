<?php
namespace CodeIgniter\HTTP;

interface ResponseInterface extends MessageInterface
{
	public function __construct(RequestInterface &$request);
	public function getStatus();
}
