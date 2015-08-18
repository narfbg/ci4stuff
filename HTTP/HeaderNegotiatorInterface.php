<?php
namespace CodeIgniter\HTTP;

interface HeaderNegotiatorInterface extends HeaderInterface
{
	public function getAcceptedValue(array $accept);
}
