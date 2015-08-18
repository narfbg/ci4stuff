<?php
namespace CodeIgniter\HTTP;

// Note: The interface is not used right now ... may remove it as an optimization
abstract class HeaderNegotiator extends HeaderMultivalue implements HeaderNegotiatorInterface
{
	const REGEXP = '(?<value>\*|([a-z]([a-z0-9]|-(?=[a-z0-9]))+))';

	public function getAcceptedValue(array $accept)
	{
		if (empty($accept))
		{
			throw new \InvalidArgumentException('List of acceptable values cannot be empty');
		}

		$input   = $this->getParsedValues();
		$matches = \array_intersect_key($input, \array_flip($accept));
		\arsort($matches, \SORT_NUMERIC);

		if (isset($input['*']))
		{
			if ((empty($matches) OR $input['*'] > \current($matches)) && $input['*'] !== 0.0)
			{
				return \array_shift($accept);
			}
		}

		if (empty($matches) OR \current($matches) === 0.0)
		{
			throw new \OutOfBoundsException('Content negotiation failed, no acceptable value was found');
		}

		return \key($matches);
	}

	public function getParsedValues()
	{
		try
		{
			$result = [];
			for ($i = 0, $c = count($this->valueArray); $i < $c; $i++)
			{
				$value = static::parseSingleValue($this->valueArray[$i]);
				$result[$value['value']] = $value['qValue'];
			}
		}
		catch (\DomainException $e)
		{
			throw new \DomainException('Invalid '.$this->getName().' header value: '.$this->valueString, 0, $e);
		}

		return $result;
	}

	public static function parseSingleValue($value)
	{
		if ( ! \is_string($value))
		{
			throw new \InvalidArgumentException('Input value must be of type string, '.\gettype($value).' given');
		}
		elseif ( ! \preg_match('#^'.static::REGEXP.'(;\s?q=(?<qValue>1(\.0{1,3})?|0(\.\d{1,3})?))?$#i', $value, $matches))
		{
			throw new \DomainException('Invalid '.$this->getName().' value: '.$value);
		}

		return [
			'value'  => \strtolower($matches['value']),
			'qValue' => isset($matches['qValue']) ? (float) $matches['qValue'] : 1.0
		];
	}
}
