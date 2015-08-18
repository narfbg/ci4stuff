<?php
namespace CodeIgniter\HTTP;

class Exception extends \Exception
{

	protected $response;
	protected $status;

	public function __construct(ResponseInterface &$response, $code = 0, $message = null, $previous = null)
	{
		if ($code === 0)
		{
			try
			{
				$status = $response->getStatus();
			}
			catch (\Exception $e)
			{
				throw new \LogicException(\get_class($this).' thrown with no status code and it was not set in the Response instance', 0, $e);
			}
		}
		else
		{
			$response->setStatus($code);
		}

		$this->response = $response;
		$status = $response->getStatus();
		$this->code = $status['code'];
		$this->message = $status['message'];
	}

	public function getCode()
	{
		return $this->status['code'];
	}

	public function getMessage()
	{
		return $this->status['message'];
	}
}
