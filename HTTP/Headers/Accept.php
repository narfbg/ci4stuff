<?php
namespace CodeIgniter\HTTP\Headers;

class Accept extends \CodeIgniter\HTTP\HeaderNegotiator
{
	const REGEXP = '(?<value>(?<topLevel>\*|[a-z]+)\/(?<subType>\*|((?<tree>[a-z]+)\.)?(?<name>[a-z0-9]+([\-\.][a-z0-9]+)*)(\+(?<suffix>[a-z0-9]+))?))';

	public function getName()
	{
		return 'Accept';
	}

	public function getAcceptedValue(array $accept)
	{
		if (empty($accept))
		{
			throw new \InvalidArgumentException('List of accepted formats cannot be empty');
		}

		try
		{
			$accept = $this->getParsedValues($accept);
		}
		catch (\DomainException $e)
		{
			throw new \InvalidArgumentException('List of accepted formats contains an invalid value', 0, $e);
		}

		// We're supposed to get all matching values, then order by quality first,
		// and "tiebreak" by specificity if we have >1 matches with the highest
		// quality. Specificity is determined by type matching first (wildcards
		// make the match less specific) and count of additional parameters later
		// (more parameters = better match).
		//
		// Separating these steps in separate algorithms may hurt performance, so
		// we'll do it all at once and compare input scores against best scores
		// first ... Chances are that higher quality/specificity inputs will come
		// first, so we should save a lot of processing.
		$bestMatch = [
			null, // Accepted string literal
			0.0,  // Quality;   Best is 1.0
			3,    // Wildcards; Best is 0
			-1    // Params;    Best is infinity ;)
		];

		foreach ($this->getParsedValues() as $inputString => &$inputArray)
		{
			if (
				$inputArray['quality'] < $bestMatch[1]
				OR ($inputArray['topLevel'] === '*' && $bestMatch[2] < 2)
				OR ($inputArray['subType'] === '*' && $bestMatch[2] === 0)
				OR ($params = \count($inputArray['params'])) <= $bestMatch[3]
			) continue;

			// Non-wildcard matches
			if ($inputArray['subType'] !== '*')
			{
				foreach ($accept as $acceptString => &$acceptArray)
				{
					// Best possible match
					if ($inputString === $acceptString)
					{
						return $acceptString;
					}
					// May be a parameter mismatch, but it may also be that $accept is more specific
					elseif (
						$inputArray['value'] === $acceptArray['value']
						&& $params > $bestMatch[3] // Another accept value in *this* foreach may have had more params matching
						&& $params === \count(\array_intersect_assoc($acceptArray['params'], $inputArray['params']))
					)
					{
						$bestMatch = [$acceptString, $inputArray['quality'], 0, $params];
					}
				}
			}
			// Partial matches (topLevel only)
			elseif ($bestMatch[2] > 0 && $inputArray['topLevel'] !== '*')
			{
				foreach ($accept as $acceptString => &$acceptArray)
				{
					if ($inputArray['topLevel'] === $acceptArray['topLevel'])
					{
						$bestMatch = [$acceptString, $inputArray['quality'], 1, $params];
						continue 2;
					}
				}
			}
			// Accepts everything (both topLevel and subType are wildcards)
			elseif ($bestMatch[2] > 1)
			{
				$bestMatch = [\array_keys($accept)[0], $inputArray['quality'], 2, $params];
			}
		}

		if ( ! isset($bestMatch[0]) OR $bestMatch[1] === 0.0)
		{
			throw new \DomainException('Content negotiation failed, no acceptable format was found');
		}

		return $bestMatch[0];
	}

	public function getParsedValues(array $values = null)
	{
		$isInput = ! isset($values);
		isset($values) OR $values = $this->valueArray;
		$error = function() { throw new \DomainException('Invalid content type value'); };
		$result = [];
		foreach ($values as &$value)
		{
			$value = static::parseSingleValue($value, $isInput);
			$key = $value['value'];

			if ( ! empty($value['params']))
			{
				\array_walk($value['params'], function(&$value, $key) { $value = $key.'='.$value; });
				$key .= ';'.\implode(';', $value['params']);
			}

			$result[$key] = $value;
		}

		return $result;
	}

	public static function parseSingleValue($value, $isInput = false)
	{
		if ( ! \is_string($value))
		{
			throw new \InvalidArgumentException('Input value must be of type string, '.\gettype($value).' given');
		}
		elseif ( ! \is_bool($isInput))
		{
			throw new \InvalidArgumentException('"Is input" flag must be boolean, '.\gettype($isInput).' given');
		}

		$pattern = static::REGEXP;

		($isInput !== false) && $pattern .= '(;\s*q=(?<quality>1(\.0)?|0(\.[0-9]{1,3})?)';

		$pattern .= '(?<params>(;\s*[a-z]+=[^;\s]+)*)?';

		($isInput !== false) && $pattern .= ')?';

		if (
			! \preg_match('#^'.$pattern.'$#', \strtolower($value), $matches)
			OR ($matches['topLevel'] === '*' && $matches['subType'] !== '*')
			OR ($isInput === false && ($matches['topLevel'] === '*' OR $matches['subType'] === '*'))
		)
		{

			throw new \DomainException('Invalid Accept value: '.$value);
		}

		$result = [
			'value'    => $matches['value'],
			'topLevel' => $matches['topLevel'],
			'subType'  => $matches['subType']
		];

		if ($isInput !== false)
		{
			$result['quality'] = isset($matches['quality'])
				? (float) $matches['quality']
				: 1.0;
		}

		$result['params'] = [];
		if (empty($matches['params']))
		{
			return $result;
		}

		foreach (\explode(';', \ltrim($matches['params'], '; ')) as $param)
		{
			// WARNING: The leading space in the pattern is important!
			list($name, $value) = \sscanf($param, ' %[^=]=%s');
			$result['params'][\strtolower($name)] = $value;
		}

		\ksort($result['params'], \SORT_STRING);
		return $result;
	}
}
