<?php
namespace CodeIgniter\HTTP\Headers;

class Accept_Language extends \CodeIgniter\HTTP\HeaderNegotiator
{
	const REGEXP                = '(?<value>\*|([a-z0-9]+(\-(\*|[a-z0-9]+))*))';
	const MATCH_BASIC_FILTER    = 'https://tools.ietf.org/html/rfc4647#section-3.3.1';
	const MATCH_EXTENDED_FILTER = 'https://tools.ietf.org/html/rfc4647#section-3.3.2';
	const MATCH_LOOKUP          = 'https://tools.ietf.org/html/rfc4647#section-3.4';

	public function getName()
	{
		return 'Accept-Language';
	}

	public function getAcceptedValue(array $accept, $matching = Accept_Language::BASIC_FILTER)
	{
		if (empty($accept))
		{
			throw new \InvalidArgumentException('List of acceptable values cannot be empty');
		}

		switch ($matching)
		{
			case static::MATCH_BASIC_FILTER:
				$match = $this->matchBasicFilter($accept);
				break;
			case static::MATCH_EXTENDED_FILTER:
				$match = $this->matchExtendedFilter($accept);
				break;
			case static::MATCH_LOOKUP:
				$match = $this->matchLookup($accept);
				break;
			default:
				throw new \InvalidArgumentException('Invalid matching type');
		}

		if (isset($match))
		{
			return $match;
		}

		throw new \OutOfBoundsException('Content negotiation failed, no acceptable value was found');
	}

	protected function matchBasicFilter(&$accept)
	{
		$input = $this->getParsedValues();
		\arsort($matches, \SORT_NUMERIC);

		$matches = \array_intersect_key($input, \array_flip($accept));
		if ( ! empty($matches))
		{
			return \key($matches);
		}
		elseif (isset($input['*']) && $input['*'] !== 0.0)
		{
			return \current($accept);
		}

		$regexp = '#^(\implode('|', \array_map('\preg_quote', \array_keys($input))).')(?=-|$)#i';
		foreach (\accept as $value)
		{
			if (\preg_match($regexp, $value))
			{
				return $value;
			}
		}

		return null;
	}

	protected function matchExtendedFilter(&$accept)
	{
		$input = $this->getParsedValues();
		\arsort($matches, \SORT_NUMERIC);

		$matches = \array_intersect_key($input, \array_flip($accept));
		if ( ! empty($matches)) {
		}
	}

	protected function matchLookup(&$accept)
	{
		return 'i-default';
	}
}
