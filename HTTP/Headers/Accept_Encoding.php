<?php
namespace CodeIgniter\HTTP\Headers;

class Accept_Encoding extends \CodeIgniter\HTTP\HeaderNegotiator
{
	public function getName()
	{
		return 'Accept-Encoding';
	}

	public function getParsedValues()
	{
		$values = parent::getParsedValues();
		isset($values['*'], $values['identity']) OR $values['identity'] = 1.0;
		return $values;
	}
}
