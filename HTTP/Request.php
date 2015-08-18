<?php
namespace CodeIgniter\HTTP;

class Request implements RequestInterface
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

	protected $method;
	protected $uri;

	public function __construct($method, $uri, $protocolVersion)
	{
		if ( ! \in_array($protocolVersion, ['HTTP/1.0', 'HTTP/1.1'], true))
		{
			throw new \RuntimeException('Unsupported HTTP version');
		}

		$this->method = $method;
		$this->uri = $uri;
		$this->protocolVersion = $protocolVersion;

		$this->headers = new HeaderCollection(HeaderCollection::FOR_REQUEST);
	}

	public function getStartLine()
	{
		return $this->method.' '.$this->uri.' '.$this->protocolVersion;
	}

	public function getMethod()
	{
		return $this->method;
	}

	public function getURI()
	{
		return $this->uri;
	}
}
