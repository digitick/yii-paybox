<?php

/**
 * Represents a Paybox direct payment response.
 * 
 * @author Ianaré Sévi
 * @license http://http://www.gnu.org/copyleft/lesser.html LGPL
 * @copyright © 2013 Digitick, S.A.
 */
class PayboxDirectResponse
{
	// response codes
	const RETURN_OK = '00000';
	const RETURN_CONNECTION_FAILED = '00001';
	const RETURN_COHERENCE_ERROR = '00002';
	const RETURN_PAYBOX_ERROR = '00003';
	const RETURN_INVALID_CARD_NUMBER = '00004';
	const RETURN_INVALID_QUESTION_NUMBER = '00005';
	const RETURN_ACCESS_REFUSED = '00006';
	const RETURN_INVALID_DATE = '00007';
	const RETURN_INVALID_EXPIRATION_DATE = '00008';
	const RETURN_INVALID_OPERATION = '00009';
	const RETURN_UNKNOWN_CURRENCY = '00010';
	const RETURN_INCORRECT_AMOUNT = '00011';
	const RETURN_INVALID_REFERENCE = '00012';
	const RETURN_UNSUPPORTED_VERSION = '00013';
	const RETURN_INCOHERENT_REQUEST = '00014';
	const RETURN_REFERENCE_ERROR = '00015';
	const RETURN_TRANSACTION_NOT_FOUND = '00018';
	const RETURN_RESERVED = '00019';
	const RETURN_MISSING_CVV = '00020';
	const RETURN_UNAUTHORIZED_CARD = '00021';
	const REPONSE_CONNECTION_TIMEOUT = '00097';
	const RETURN_INTERNAL_CONNECTION_ERROR = '00098';
	const RETURN_INCOHERENT_RETURN = '00099';
	// parameters
	const PARAM_TRANSACTION_NUMBER = 'NUMTRANS';
	const PARAM_CALL_NUMBER = 'NUMAPPEL';
	const PARAM_NUMQUESTION = 'NUMQUESTION';
	const PARAM_SITE = 'SITE';
	const PARAM_RANK = 'RANG';
	const PARAM_IDENTIFIER = 'IDENTIFIANT';
	const PARAM_AUTHORIZATION = 'AUTORISATION';
	const PARAM_RESPONSE_CODE = 'CODEREPONSE';
	const PARAM_COMMENT = 'COMMENTAIRE';
	const PARAM_COUNTRY = 'PAYS';
	const PARAM_CARD_TYPE = 'TYPECARTE';
	const PARAM_SHA1 = 'SHA-1';
	const PARAM_STATUS = 'STATUS';
	const PARAM_REMIT_ID = 'REMISE';

	/**
	 * @var string 10 NUMTRANS Number of the transaction created on Paybox.
	 */
	public $transactionNumber;
	/**
	 * @var string 10 NUMAPPEL Number of the request handled on Paybox.
	 */
	public $callNumber;
	/**
	 * @var string 10 NUMQUESTION single request identifier which prevents
	 * confusion over the replies in the case of multiple and simultaneous
	 * questions.
	 */
	public $questionNumber;
	/**
	 * @var string 7 SITE membership number supplied by the retailer's bank.
	 */
	public $site;
	/**
	 * @var string 2 RANG site rank number supplied by the retailer's bank.
	 */
	public $rank;
	/**
	 * @var string 10 IDENTIFIANT membership number supplied by American Express
	 * or Diners Club for the administration of their cards.
	 */
	public $identifier;
	/**
	 * @var string var10 AUTORISATION authorization number granted by the
	 * authorization centre of the retailer's bank if the payment is accepted.
	 */
	public $authorization;
	/**
	 * @var string 5 CODEREPONSE reply code concerning the status of the
	 * question treated.
	 */
	public $responseCode;
	/**
	 * @var string var100 COMMENTAIRE Supply messages of information.
	 */
	public $comment;
	/**
	 * @var string 3 PAYS the country code of the issuer (bank of the cardholder).
	 * 
	 * The value "???" means an unknown code.
	 */
	public $country;
	/**
	 * @var string 10 TYPECARTE The type card used for the payment.
	 */
	public $cardType;
	/**
	 * @var string 40 SHA-1 The SHA-1 digest of the card number.
	 */
	public $sha1;
	/**
	 * @var string var16 STATUS The state of the transaction. Only for the
	 * type 17 in the question frame.
	 */
	public $status;
	/**
	 * @var string var9 REMISE The ID PAYBOX of the remittance. Only for the
	 * type 17 in the question frame.
	 */
	public $remmitId;
	protected $error = false;
	protected $rawData;

	public function __construct($data)
	{
		$this->populate($data);
	}

	/**
	 * Populate the object with the response data received.
	 * Called on construction.
	 * @param string $data 
	 */
	public function populate($data)
	{
		$dataArray = array();
		parse_str(utf8_encode($data), $dataArray);

		if (isset($dataArray[PayboxDirectResponse::PARAM_TRANSACTION_NUMBER])) {
			$this->transactionNumber = $dataArray[PayboxDirectResponse::PARAM_TRANSACTION_NUMBER];
		}
		if (isset($dataArray[PayboxDirectResponse::PARAM_CALL_NUMBER])) {
			$this->callNumber = $dataArray[PayboxDirectResponse::PARAM_CALL_NUMBER];
		}
		if (isset($dataArray[PayboxDirectResponse::PARAM_NUMQUESTION])) {
			$this->questionNumber = $dataArray[PayboxDirectResponse::PARAM_NUMQUESTION];
		}
		if (isset($dataArray[PayboxDirectResponse::PARAM_SITE])) {
			$this->site = $dataArray[PayboxDirectResponse::PARAM_SITE];
		}
		if (isset($dataArray[PayboxDirectResponse::PARAM_RANK])) {
			$this->rank = $dataArray[PayboxDirectResponse::PARAM_RANK];
		}
		if (isset($dataArray[PayboxDirectResponse::PARAM_IDENTIFIER])) {
			$this->identifier = $dataArray[PayboxDirectResponse::PARAM_IDENTIFIER];
		}
		if (isset($dataArray[PayboxDirectResponse::PARAM_AUTHORIZATION])) {
			$this->authorization = $dataArray[PayboxDirectResponse::PARAM_AUTHORIZATION];
		}
		if (isset($dataArray[PayboxDirectResponse::PARAM_RESPONSE_CODE])) {
			$this->responseCode = $dataArray[PayboxDirectResponse::PARAM_RESPONSE_CODE];
		}
		if (isset($dataArray[PayboxDirectResponse::PARAM_COMMENT])) {
			$this->comment = $dataArray[PayboxDirectResponse::PARAM_COMMENT];
		}
		if (isset($dataArray[PayboxDirectResponse::PARAM_COUNTRY])) {
			$this->country = $dataArray[PayboxDirectResponse::PARAM_COUNTRY];
		}
		if (isset($dataArray[PayboxDirectResponse::PARAM_CARD_TYPE])) {
			$this->cardType = $dataArray[PayboxDirectResponse::PARAM_CARD_TYPE];
		}
		if (isset($dataArray[PayboxDirectResponse::PARAM_SHA1])) {
			$this->sha1 = $dataArray[PayboxDirectResponse::PARAM_SHA1];
		}
		if (isset($dataArray[PayboxDirectResponse::PARAM_STATUS])) {
			$this->status = $dataArray[PayboxDirectResponse::PARAM_STATUS];
		}
		if (isset($dataArray[PayboxDirectResponse::PARAM_REMIT_ID])) {
			$this->remmitId = $dataArray[PayboxDirectResponse::PARAM_REMIT_ID];
		}

		$this->rawData = $dataArray;
		$this->logInfo(print_r($dataArray, true));

		$this->setError();
	}

	/**
	 * Get the response error.
	 * @return mixed False if no error, error array otherwise. 
	 */
	public function getError()
	{
		return $this->error;
	}

	/**
	 * Get whether the return contains an error.
	 * @return boolean 
	 */
	public function hasError()
	{
		return ($this->error !== false);
	}

	/**
	 * @return array 
	 */
	public function getRawData()
	{
		return $this->rawData;
	}
	
	/**
	 * Set the error array.
	 */
	protected function setError()
	{
		if ($this->responseCode != self::RETURN_OK) {
			$msg = $this->getErrorMessage($this->responseCode);
			$code = $this->responseCode;
			$this->error = array(
				'code' => $code,
				'message' => $msg
			);
			Yii::log("Transaction: {$this->transactionNumber} | Error: $code - $msg | Comment: {$this->comment}", CLogger::LEVEL_ERROR, 'ext.paybox.DirectResponse');
		}
		else
			$this->error = false;
	}

	/**
	 * Log an info message.
	 * @param string $msg Message to log.
	 */
	protected function logInfo($msg)
	{
		Yii::log("transaction: {$this->transactionNumber} | $msg", CLogger::LEVEL_INFO, 'ext.paybox.DirectResponse');
	}

	protected function getErrorMessage($code)
	{
		$returnCodes = array(
			self::RETURN_CONNECTION_FAILED => Yii::t('paybox', 'Connection to the authorization center has failed.'),
			self::RETURN_COHERENCE_ERROR => Yii::t('paybox', 'An error in coherence has occurred.'),
			self::RETURN_PAYBOX_ERROR => Yii::t('paybox', 'Paybox error.'),
			self::RETURN_INVALID_CARD_NUMBER => Yii::t('paybox', 'The card number specified is invalid.'),
			self::RETURN_INVALID_QUESTION_NUMBER => Yii::t('paybox', 'Invalid question number.'),
			self::RETURN_ACCESS_REFUSED => Yii::t('paybox', 'Access refused or site/rank incorrect.'),
			self::RETURN_INVALID_DATE => Yii::t('paybox', 'The date specified is invalid.'),
			self::RETURN_INVALID_EXPIRATION_DATE => Yii::t('paybox', 'The specified card expiration date is invalid.'),
			self::RETURN_INVALID_OPERATION => Yii::t('paybox', 'Invalid operation type.'),
			self::RETURN_UNKNOWN_CURRENCY => Yii::t('paybox', 'Unkown currency.'),
			self::RETURN_INCORRECT_AMOUNT => Yii::t('paybox', 'The amount specified is incorrect.'),
			self::RETURN_INVALID_REFERENCE => Yii::t('paybox', 'Invalid order reference.'),
			self::RETURN_UNSUPPORTED_VERSION => Yii::t('paybox', 'This version is no longer supported.'),
			self::RETURN_INCOHERENT_REQUEST => Yii::t('paybox', 'Incoherent request sent.'),
			self::RETURN_REFERENCE_ERROR => Yii::t('paybox', 'Error accessing previously referenced data.'),
			self::RETURN_TRANSACTION_NOT_FOUND => Yii::t('paybox', 'Transaction not found.'),
			self::RETURN_MISSING_CVV => Yii::t('paybox', 'CVV not present.'),
			self::RETURN_UNAUTHORIZED_CARD => Yii::t('paybox', 'The card specified is not authorized for the payment.'),
			self::REPONSE_CONNECTION_TIMEOUT => Yii::t('paybox', 'Connection timeout.'),
			self::RETURN_INTERNAL_CONNECTION_ERROR => Yii::t('paybox', 'Internal connection error.'),
			self::RETURN_INCOHERENT_RETURN => Yii::t('paybox', 'Incoherence between the question and the answer. Retry later.'),
		);
		if (in_array($code, array_keys($returnCodes))) {
			$msg = $returnCodes[$this->responseCode];
		}
		else {
			$msg = Yii::t('paybox', 'Your payment has been refused.');
		}
		return $msg;
	}

}