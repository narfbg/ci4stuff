<?php
namespace CodeIgniter\HTTP\Headers;

class Accept_Charset extends \CodeIgniter\HTTP\HeaderNegotiator
{
	public function getName()
	{
		return 'Accept-Charset';
	}

/*
	NOTES:
		- RFC7231 (HTTP/1.1 updated):

			If an Accept-Charset header field is present in a request and none of
			the available representations for the response has a charset that is
			listed as acceptable, the origin server can either honor the header
			field, by sending a 406 (Not Acceptable) response, or disregard the
			header field by treating the resource as if it is not subject to
			content negotiation.

		- RFC2616 (HTTP/1.1 original):

			If no "*" is present in an Accept-Charset field, then all character
			sets not explicitly mentioned get a quality value of 0, except for
			ISO-8859-1, which gets a quality value of 1 if not explicitly
			mentioned.

		- http://www.w3.org/Protocols/HTTP/1.0/spec.html#Accept-Charset

			The Accept-Charset request-header field can be used to indicate a
			list of preferred character sets other than the default US-ASCII and
			ISO-8859-1. This field allows clients capable of understanding more
			comprehensive or special-purpose character sets to signal that
			capability to a server which is capable of representing documents in
			those character sets.

	public function getParsedValues() {

		$values = parent::getParsedValues();
		isset($values['*'], $values['iso-8859-1']) OR $values['iso-8859-1'] = 1.0;
		return $values;
	}
*/
}
