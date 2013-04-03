<?php

/**
 * Perform a Paybox direct payment.
 * 
 * @author Ianaré Sévi
 * @license http://http://www.gnu.org/copyleft/lesser.html LGPL
 * @copyright © 2013 Digitick, S.A.
 */
class EPayboxDirectPayment extends CComponent
{
	/**
	 * @var string Version number of the PPPS (defaults to 00103).
	 */
	public $version = '00103';

	/**
	 * @var integer Site rank number as supplied by the retailer's bank.
	 *
	 * 2 numbers, test: 99
	 */
	public $rank = 99;

	/**
	 * @var integer Membership number as supplied by the retailer's bank.
	 *
	 * 7 numbers, test: 1999888
	 */
	public $site = '1999888';

	/**
	 * @var string Unique key as given by Paybox.
	 *
	 * Test: 1999888I
	 */
	public $key = '1999888I';

	/**
	 * @var boolean
	 */
	public $debug;

	/**
	 * @var PayboxDirectRequest
	 */
	protected $request;

	/**
	 * @var PayboxDirectResponse
	 */
	protected $response;

	/**
	 * @var array
	 */
	protected $error;

	/**
	 * Generate a unique call number to identify each request in one day.
	 *
	 * This method should probably be extended, as this implementation is likely
	 * to cause problems on high volumme web sites.
	 * @return string
	 */
	protected function generateRequestNumber()
	{
		// seconds since midnight
		$secs = (date('G') * 3600) + (date('i') * 60) + date('s');
		return (string) $secs . rand(10, 99);
	}

	/**
	 * @return PayboxDirectRequest
	 */
	public function getRequest()
	{
		return $this->request;
	}

	/**
	 * @return PayboxDirectResponse
	 */
	public function getResponse()
	{
		return $this->response;
	}

	public function getError()
	{
		if ($this->error)
			return $this->error;
		else
			return array(
				'response' => $this->response->getError()
			);
	}

	public function initializeRequest(array $values)
	{
		$defaults = array(
			'version' => $this->version,
			'rank' => $this->rank,
			'site' => $this->site,
			'key' => $this->key,
			'requestNumber' => $this->generateRequestNumber(),
		);
		$values = array_merge($values, $defaults);

		$request = new PayboxDirectRequest;
		$request->setAttributes($values);
		$request->setDebug($this->debug);

		$this->request = $request;
	}

	public function authorizePayment()
	{
		return $this->sendRequest(PayboxDirectRequest::TYPE_AUTHORIZATION);
	}

	public function sendPayment()
	{
		return $this->sendRequest(PayboxDirectRequest::TYPE_DEBIT);
	}

	public function authorizeAndSendPayment()
	{
		return $this->sendRequest(PayboxDirectRequest::TYPE_AUTORIZATION_DEBIT);
	}

	public function sendCredit()
	{
		return $this->sendRequest(PayboxDirectRequest::TYPE_CREDIT);
	}

	public function cancelTransaction()
	{
		return $this->sendRequest(PayboxDirectRequest::TYPE_CANCELLATION);
	}

	public function checkTransaction()
	{
		return $this->sendRequest(PayboxDirectRequest::TYPE_CHECK_TRANSACTION);
	}

	public function sendPaymentNoAuth()
	{
		return $this->sendRequest(PayboxDirectRequest::TYPE_TRANSACTION_NO_AUTH);
	}

	public function modifyTransactionAmount()
	{
		return $this->sendRequest(PayboxDirectRequest::TYPE_MODIFY_TRANS_AMOUNT);
	}

	public function refundTransaction()
	{
		return $this->sendRequest(PayboxDirectRequest::TYPE_REFUND);
	}

	public function lookupTransaction()
	{
		return $this->sendRequest(PayboxDirectRequest::TYPE_LOOKUP);
	}

	/**
	 * Send the request frame.
	 * @param type $type
	 * @return boolean
	 * @throws CException
	 */
	protected function sendRequest($type)
	{
		if (!$this->request)
			throw new CException('Request is not initialized.');

		$this->request->type = $type;
		$response = $this->request->send();

		// response received
		if ($response !== false) {
			$this->response = $response;
			// response received with error
			if ($this->response->hasError())
				return false;
			// all good
			return true;
		}
		// no response: request was invalid
		else {
			$this->error = $this->request->getErrors();
			return false;
		}
	}

}

