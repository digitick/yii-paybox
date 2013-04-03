<?php

/**
 * Represents a Paybox direct payment request.
 * 
 * @author Ianaré Sévi
 * @license http://http://www.gnu.org/copyleft/lesser.html LGPL
 * @copyright © 2013 Digitick, S.A.
 */
class PayboxDirectRequest extends CFormModel
{
	const HOST = 'https://ppps.paybox.com/PPPS.php';
	const HOST_BACKUP_1 = 'https://ppps1.paybox.com/PPPS.php';
	const HOST_BACKUP_2 = 'https://ppps2.paybox.com/PPPS.php';
	const HOST_DEBUG = 'https://preprod-ppps.paybox.com/PPPS.php';

	// transaction types
	const TYPE_AUTHORIZATION = '00001';
	const TYPE_DEBIT = '00002';
	const TYPE_AUTORIZATION_DEBIT = '00003';
	const TYPE_CREDIT = '00004';
	const TYPE_CANCELLATION = '00005';
	const TYPE_CHECK_TRANSACTION = '00011';
	const TYPE_TRANSACTION_NO_AUTH = '00012';
	const TYPE_MODIFY_TRANS_AMOUNT = '00013';
	const TYPE_REFUND = '00014';
	const TYPE_LOOKUP = '00017';
	// activity types
	const ACTIVITY_NON_SPECIFIED = '020';
	const ACTIVITY_TELEPHONE = '021';
	const ACTIVITY_CORRESPONDANCE = '022';
	const ACTIVITY_MINITEL = '023';
	const ACTIVITY_INTERNET = '024';
	const ACTIVITY_RECURRENT = '027';

	/**
	 * @var string VERSION Version number of the PPPS (defaults to 00103).
	 */
	public $version;

	/**
	 * @var string DATEQ Request timestamp in the format: ddmmyyyyhhmmss
	 */
	public $dateq;

	/**
	 * @var string TYPE Request type.
	 */
	public $type;

	/**
	 * @var integer NUMQUESTION Unique identifier for the request to avoid
	 * confusing responses in case of multiple simultaneous requests.
	 *
	 * This number may be safely reset every day.
	 */
	public $requestNumber;

	/**
	 * @var integer SITE Membership number as supplied by the retailer's bank.
	 * Test: 1999888
	 */
	public $site;

	/**
	 * @var integer RANG Site rank number as supplied by the retailer's bank.
	 * Test: 99
	 */
	public $rank;

	/**
	 * @var string CLE Unique key as given by Paybox.
	 * Test: 1999888I
	 */
	public $key;

	/**
	 * @var string IDENTIFIANT Empty field, not used at the moment.
	 */
	public $identifier;

	/**
	 * @var integer MONTANT Amount of the transaction in cents (no commas or decimal points).
	 */
	public $amountCents;

	/**
	 * @var string|integer DEVISE Currency ISO code, 3 letter or numerical.
	 * 978 or EUR for the Euro.
	 */
	public $currency;

	/**
	 * @var string REFABONNE Retailer's reference which enables the clear identification
	 * of the subscriber corresponding to the transaction.
	 * 250 Characters maximum.
	 */
	public $subscriberRef;

	/**
	 * @var string REFERENCE Retailer's reference which enables the clear
	 * identification of the order corresponding to the transaction.
	 * 250 Characters maximum.
	 */
	public $reference;

	/**
	 * @var string PORTEUR Cardholder (customer) card number, without spaces, left justified.
	 * In cases of registration or of modification, the holder's partial number
	 * should be left justified.
	 * Test: 1111222233334444
	 */
	public $ccNumber;

	/**
	 * @var integer DATEVAL Expiration date of the cardholder's card in format: MMYY
	 */
	public $ccExpDate;

	/**
	 * @var integer CVV Visual cryptogram located on the back on the bank card.
	 * 3 or 4 characters
	 */
	public $cvvCode;

	/**
	 * @var string ACTIVITE Electronic commerce indicator (ECI) enabling the
	 * provenance of the various electronic money movements to be distinguished.
	 */
	public $activity;

	/**
	 * @var string ARCHIVAGE Filing reference given to your bank.
	 *
	 * It should be unique and can allow to your bank to supply you an
	 * information in case of chargeback.
	 */
	public $archiveId;

	/**
	 * @var integer DIFFERE Number of days before to send the transaction at your bank
	 * in order to credit your bank account.
	 */
	public $waitDays;

	/**
	 * @var integer NUMAPPEL Number returned by Paybox in the response.
	 *
	 * This field must be filled in on the next request if it
	 * concerns a request for capture or cancellation.
	 *
	 * For other types of request (1, 3 or 4), this field remains empty.
	 */
	public $callNumber;

	/**
	 * @var integer NUMTRANS Number returned by Paybox in the response when
	 * handling a payment likely to be sent to the bank.
	 *
	 * This field must be filled in on the next request if it
	 * concerns a request for capture or cancellation.
	 *
	 * For other types of request (1, 3 or 4), this field remains empty.
	 */
	public $transactionNumber;

	/**
	 * @var string Authorization number as provided by the merchant following a
	 * phone call to the bank.
	 */
	public $authorization;

	/**
	 * @var boolean PAYS Indicate whether the country code should be returned.
	 */
	public $countryCode;

	/**
	 * @var string Value provided by the merchant to indicate payment options
	 * on SOFINCO or COFINOGA cards.
	 */
	public $paymentOption;

	/**
	 * @var integer Date of birth of the cardholder for the payment with
	 * COFINOGA card.
	 * MMDDYYYY
	 */
	public $birthdate;

	/**
	 * @var string Value provided by the merchant in order to cancel or capture a payment via PayPal.
	 */
	public $paypalId;

	/**
	 * @var boolean TYPECARTE Indicate whether the card type should be returned.
	 */
	public $cardType;

	/**
	 * @var boolean SHA1 Indicate whether the card number sha-1 should be returned.
	 */
	public $sha1;

	/**
	 * @var string ID3D Context ID with the authenticate datas from the MPI.
	 */
	public $id3d;

	/**
	 * @var integer ERRORCODETEST Error code to return in the
	 * pre-production/tests environment.
	 *
	 * Ignored in the production environment.
	 */
	public $errorCodeTest;

	/**
	 * @var boolean In debug mode, use the Paybox test URL.
	 * If not set, uses the YII_DEBUG value.
	 */
	protected $debug;

	public function init()
	{
		$this->dateq = date('dmYHis');
	}

	public function rules()
	{
		return array(
			array('version, dateq, type, requestNumber, site, rank, amountCents, currency', 'required'),
			array('countryCode, cardType, sha1', 'boolean'),
			//
			array('version', 'length', 'max' => 5),
			array('version', 'numerical'),
			//
			array('dateq', 'numerical'),
			//
			array('site', 'length', 'is' => 7),
			array('site', 'numerical'),
			//
			array('rank', 'length', 'is' => 2),
			array('rank', 'numerical'),
			//
			array('key', 'checkKeyRequired'),
			array('key', 'length', 'is' => 8),
			//
			array('amountCents', 'padLeft', 'length' => 10),
			array('amountCents', 'numerical', 'integerOnly' => true),
			array('amountCents', 'length', 'is' => 10),
			//
			array('currency', 'checkCurrency'),
			//
			array('reference', 'checkReferenceRequired'),
			array('reference, subscriberRef', 'length', 'max' => 250),
			//
			array('ccNumber', 'checkCcNumberRequired'),
			array('ccNumber', 'length', 'min' => 14, 'max' => 19),
			array('ccNumber', 'numerical', 'integerOnly' => true),
			//
			array('ccExpDate', 'checkCcExpDateRequired'),
			array('ccExpDate', 'length', 'is' => 4),
			array('ccExpDate', 'numerical', 'integerOnly' => true),
			//
			array('cvvCode', 'checkCvvCodeRequired'),
			array('cvvCode', 'length', 'min' => 3, 'max' => 4),
			array('cvvCode', 'numerical', 'integerOnly' => true),
			//
			array('activity', 'in', 'range' => array(
					self::ACTIVITY_CORRESPONDANCE,
					self::ACTIVITY_INTERNET,
					self::ACTIVITY_MINITEL,
					self::ACTIVITY_NON_SPECIFIED,
					self::ACTIVITY_RECURRENT,
					self::ACTIVITY_TELEPHONE
			)),
			//
			array('type', 'in', 'range' => array(
					self::TYPE_CANCELLATION,
					self::TYPE_AUTHORIZATION,
					self::TYPE_AUTORIZATION_DEBIT,
					self::TYPE_LOOKUP,
					self::TYPE_CREDIT,
					self::TYPE_DEBIT,
					self::TYPE_MODIFY_TRANS_AMOUNT,
					self::TYPE_REFUND,
					self::TYPE_TRANSACTION_NO_AUTH,
					self::TYPE_CHECK_TRANSACTION,
			)),
			//
			array('archiveId', 'length', 'max' => 12),
			//
			array('requestNumber', 'padLeft', 'length' => 10),
			array('requestNumber', 'numerical', 'integerOnly' => true),
			array('requestNumber', 'length', 'is' => 10),
			//
			array('transactionNumber', 'checkTransactionNumberRequired'),
			array('transactionNumber', 'padLeft', 'length' => 10),
			array('transactionNumber', 'numerical', 'integerOnly' => true),
			array('transactionNumber', 'length', 'is' => 10),
			//
			array('callNumber', 'checkCallNumberRequired'),
			array('callNumber', 'padLeft', 'length' => 10),
			array('callNumber', 'numerical', 'integerOnly' => true),
			array('callNumber', 'length', 'is' => 10),
			//
			array('waitDays', 'padLeft', 'length' => 3),
			array('waitDays', 'numerical', 'integerOnly' => true),
			//
			array('errorCodeTest', 'padLeft', 'length' => 5),
			array('errorCodeTest', 'numerical', 'integerOnly' => true),
		);
	}

	/**
	 * Pad strings left.
	 * @param string $attribute
	 * @param array $params
	 */
	public function padLeft($attribute, $params)
	{
		if ($this->{$attribute})
			$this->{$attribute} = str_pad($this->{$attribute}, $params['length'], '0', STR_PAD_LEFT);
	}

	/**
	 * Check and set currency. ISO code in numeric or string format may be used.
	 */
	public function checkCurrency()
	{
		$currencies = require 'currencyCodes.php';

		if (is_bool($this->currency))
			$this->addError('currency', 'Currency may not be a boolean value.');

		else if (is_numeric($this->currency) && !in_array($this->currency, $currencies))
			$this->addError('currency', 'Invalid currency number code specified.');

		else if (!is_numeric($this->currency)) {
			$code = strtoupper($this->currency);
			if (array_key_exists($code, $currencies))
				$this->currency = $currencies[$code];
			else
				$this->addError('currency', 'Invalid currency letter code specified: ' . $this->currency);
		}
		else
			$this->addError('currency', 'Invalid value for currency.');
	}

	/**
	 * Check if the key is required.
	 */
	public function checkKeyRequired()
	{
		if ($this->version >= '00103' && !$this->key)
			$this->addError('key', 'Key is required.');
	}

	/**
	 * Check if the reference is required.
	 */
	public function checkReferenceRequired()
	{
		if (!$this->reference && $this->type != self::TYPE_MODIFY_TRANS_AMOUNT)
			$this->addError('reference', 'Reference is required.');
	}

	/**
	 * Check if the card number is required.
	 */
	public function checkCcNumberRequired()
	{
		if (!$this->ccNumber && in_array($this->type, array(
					self::TYPE_AUTHORIZATION,
					self::TYPE_AUTORIZATION_DEBIT,
					self::TYPE_CREDIT,
					self::TYPE_TRANSACTION_NO_AUTH))
		) {
			$this->addError('ccNumber', 'Card Number is required.');
		}
	}

	/**
	 * Check if the card expiration date is required.
	 */
	public function checkCcExpDateRequired()
	{
		if (!$this->ccExpDate && in_array($this->type, array(
					self::TYPE_AUTHORIZATION,
					self::TYPE_AUTORIZATION_DEBIT,
					self::TYPE_CREDIT,
					self::TYPE_CANCELLATION,
					self::TYPE_TRANSACTION_NO_AUTH)
		)) {
			$this->addError('ccExpDate', 'Card Expiration Date is required.');
		}
	}

	/**
	 * Check if the card CVV is required.
	 */
	public function checkCvvCodeRequired()
	{
		if (!$this->cvvCode && in_array($this->type, array(
					self::TYPE_AUTHORIZATION,
					self::TYPE_AUTORIZATION_DEBIT,
					self::TYPE_CREDIT,
					self::TYPE_CANCELLATION,
					self::TYPE_TRANSACTION_NO_AUTH
				))) {
			$this->addError('cvvCode', 'Card CCV Code is required.');
		}
	}

	/**
	 * Check if the call number is required.
	 */
	public function checkCallNumberRequired()
	{
		if (!$this->callNumber && !in_array($this->type, array(
					self::TYPE_AUTHORIZATION,
					self::TYPE_AUTORIZATION_DEBIT,
					self::TYPE_CREDIT,
				))) {
			$this->addError('callNumber', 'Call number is required.');
		}
	}

	/**
	 * Check if the transaction number is required.
	 */
	public function checkTransactionNumberRequired()
	{
		if (!$this->transactionNumber && in_array($this->type, array(
					self::TYPE_DEBIT,
					self::TYPE_CANCELLATION,
					self::TYPE_MODIFY_TRANS_AMOUNT,
					self::TYPE_LOOKUP,
				))) {
			$this->addError('transactionNumber', 'Transaction Number is required.');
		}
	}

	/**
	 * Build the query data array to send in the request.
	 * @return array
	 */
	protected function getQueryData()
	{
		$data = array(
			'VERSION' => $this->version,
			'DATEQ' => $this->dateq,
			'TYPE' => $this->type,
			'NUMQUESTION' => $this->requestNumber,
			'SITE' => $this->site,
			'RANG' => $this->rank,
			'DEVISE' => $this->currency,
			'MONTANT' => $this->amountCents,
		);

		if ($this->key) {
			$data['CLE'] = $this->key;
		}
		if ($this->countryCode) {
			$data['PAYS'] = $this->countryCode;
		}
		if ($this->identifier) {
			$data['IDENTIFIANT'] = $this->identifier;
		}
		if ($this->reference) {
			$data['REFERENCE'] = $this->reference;
		}
		if ($this->subscriberRef) {
			$data['REFABONNE'] = $this->subscriberRef;
		}
		if ($this->ccNumber) {
			$data['PORTEUR'] = $this->ccNumber;
		}
		if ($this->ccExpDate) {
			$data['DATEVAL'] = $this->ccExpDate;
		}
		if ($this->cvvCode) {
			$data['CVV'] = $this->cvvCode;
		}
		if ($this->cardType) {
			$data['TYPECARTE'] = $this->cardType;
		}
		if ($this->activity) {
			$data['ACTIVITE'] = $this->activity;
		}
		if ($this->archiveId) {
			$data['ARCHIVAGE'] = $this->archiveId;
		}
		if ($this->transactionNumber) {
			$data['NUMTRANS'] = $this->transactionNumber;
		}
		if ($this->callNumber) {
			$data['NUMAPPEL'] = $this->callNumber;
		}
		if ($this->waitDays) {
			$data['DIFFERE'] = $this->waitDays;
		}
		if ($this->id3d) {
			$data['ID3D'] = $this->id3d;
		}
		if ($this->sha1) {
			$data['SHA1'] = $this->sha1;
		}
		if ($this->errorCodeTest) {
			$data['ERRORCODETEST'] = $this->errorCodeTest;
		}

		return $data;
	}

	/**
	 * Force the debug mode. If debug is true, the test URL is used.
	 * @param boolean $value
	 * @throws CException if given value is not a boolean.
	 */
	public function setDebug($value)
	{
		if (is_bool($value))
			$this->debug = $value;
		else
			throw new CException('Inavlid value type given for debug. Must be a boolean.');
	}

	/**
	 * Get the debug mode.
	 *
	 * If the debug property is set, use that value.
	 * If the debug property is not set, return the YII_DEBUG value.
	 * @return boolean
	 */
	public function getDebug()
	{
		if ($this->debug === true || $this->debug === false)
			return $this->debug;

		else if ($this->debug === null)
			return YII_DEBUG;
	}

	/**
	 * Send a question frame to the Paybox server(s).
	 * @return boolean|PayboxDirectResponse
	 * @throws CException on empty or no response from servers
	 */
	public function send()
	{
		if (!$this->validate())
			return false;

		$data = $this->getQueryData();

		if ($this->getDebug())
			$strResponse = $this->_curlRequest(self::HOST_DEBUG, $data);
		else {
			$strResponse = $this->_curlRequest(self::HOST, $data);
			if ($strResponse === false) {
				$strResponse = $this->_curlRequest(self::HOST_BACKUP_1, $data);
				if ($strResponse === false) {
					$strResponse = $this->_curlRequest(self::HOST_BACKUP_2, $data);
				}
			}
		}

		if ($strResponse === false)
			throw new CException('No response from Paybox servers.');

		if ($strResponse === '')
			throw new CException('Empty response from Paybox servers.');

		return new PayboxDirectResponse($strResponse);
	}

	protected function logInfo($msg)
	{
		Yii::log($msg, CLogger::LEVEL_INFO, 'ext.paybox.DirectRequest');
	}

	/**
	 * Base function for all cURL requests.
	 * @param resource $ch A cURL handle returned by curl_init().
	 * @param array $options Options for the cURL transfer
	 * @return mixed
	 */
	private function _curlRequest($url, $data)
	{
		$ch = curl_init($url);
		$options = array(
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => http_build_query($data),
			CURLOPT_HTTPHEADER => array(
				'Accept-Charset: utf-8',
				'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
			),
		);

		$this->logInfo(print_r($data, true));

		$options[CURLOPT_HEADER] = false;
		$options[CURLOPT_RETURNTRANSFER] = true;
		$options[CURLOPT_VERBOSE] = false;
		curl_setopt_array($ch, $options);

		$result = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($httpCode > 400) {
			throw new CException($result);
		}
		return $result;
	}

}