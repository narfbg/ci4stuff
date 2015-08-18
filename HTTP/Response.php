<?php
namespace CodeIgniter\HTTP;

class Response implements ResponseInterface
{
	/**
	 * Declares:
	 *
	 *	protected $protocolVersion;
	 *	protected $headers = [];
	 *	protected $body;
	 *	protected $isComplete = false;
	 *
	 *	public setHeader($name, $value);
	 *	public appendHeader($name, $value);
	 *	public removeHeader($name);
	 *	public getHeaders();
	 *	public getProtocolVersion();
	 *	public setBody(&$data);
	 *	public getBody();
	 *	public complete();
	 */
	use MessageTrait;

	protected static $statusCodes = [
		// 1xx: Informational
		100 => 'Continue',
		101 => 'Switching Protocols',

		// 2xx: Success
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information', // 1.1
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		226 => 'IM Used', // 1.1; http://www.ietf.org/rfc/rfc3229.txt

		// 3xx: Redirection
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found', // Formerly 'Moved Temporarily'
		303 => 'See Other', // 1.1
		304 => 'Not Modified',
		305 => 'Use Proxy', // 1.1
		306 => 'Switch Proxy', // No longer used
		307 => 'Temporary Redirect', // 1.1
		308 => 'Permanent Redirect', // 1.1; Experimental; http://www.ietf.org/rfc/rfc7238.txt

		// 4xx: Client error
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		418 => "I'm a teapot", // April's Fools joke; http://www.ietf.org/rfc/rfc2324.txt
		// 419 (Authentication Timeout) is a non-standard status code with unknown origin
		426 => 'Upgrade Required',
		428 => 'Precondition Required', // 1.1; http://www.ietf.org/rfc/rfc6585.txt
		429 => 'Too Many Requests', // 1.1; http://www.ietf.org/rfc/rfc6585.txt
		431 => 'Request Header Fields Too Large', // 1.1; http://www.ietf.org/rfc/rfc6585.txt

		// 5xx: Server error
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		506 => 'Variant Also Negotiates', // 1.1; http://www.ietf.org/rfc/rfc2295.txt
		510 => 'Not Extended', // http://www.ietf.org/rfc/rfc2774.txt
		511 => 'Network Authentication Required' // http://www.ietf.org/rfc/rfc6585.txt
	];

	protected $request;

	protected $statusCode;
	protected $statusMessage;

	public function __construct(RequestInterface &$request)
	{
		if ( ! \in_array($request->getProtocolVersion(), ['HTTP/1.0', 'HTTP/1.1'], true))
		{
			$this->setStatus(505);
			$this->isComplete = true;
		}

		$this->request = $request;
		$this->protocolVersion = $request->getProtocolVersion();

		$this->headers = new HeaderCollection(HeaderCollection::FOR_RESPONSE);
	}

	public function getStartLine()
	{
		$status = $this->getStatus();
		return $this->protocolVersion.' '.$status['code'].' '.$status['message'];
	}

	public function setStatus($code, $message = null)
	{
		if ($this->isComplete === true)
		{
			throw new \LogicException(\get_class($this).' instance already finalized');
		}

		if ( ! \is_int($code))
		{
			if ( ! \ctype_digit($code) OR $code < 100 OR $code > 599)
			{
				throw new \InvalidArgumentException($code.' is not a valid HTTP return status code');
			}

			$code = (int) $code;
		}

		if ( ! isset($message))
		{
			if ( ! isset(static::$statusCodes[$code]))
			{
				throw new \InvalidArgumentException('Unknown HTTP status code provided with no message');
			}

			$message = static::$statusCodes[$code];
		}

		$this->statusCode = $code;
		$this->statusMessage = $message;

		return $this;
	}

	public function getStatus()
	{
		if ( ! isset($this->statusCode))
		{
			throw new \BadMethodCallException('HTTP Response is missing a status code');
		}
		elseif ( ! isset($this->statusMessage, static::$statusCodes[$this->statusCode]))
		{
			throw new \RuntimeException('HTTP Response is missing status message for code '.$this->statusCode);
		}

		return [
			'code'    => $this->statusCode,
			'message' => $this->statusMessage
		];
	}

	public function getRequest() {

		return $this->request;
	}
}
