yii-paybox
==========

Paybox service connector for Yii.

##Requirements

Yii 1.1.x

Before using this extension you must first have created an account with
[Paybox](http://www.paybox.com).


##Usage

For now only Paybox Direct is supported.

```php
$paybox = new EPayboxDirectPayment;
$paybox->debug = YII_DEBUG;
$paybox->site = Yii::app()->params['payboxSite'];
$paybox->rank = Yii::app()->params['payboxRank'];
$paybox->key = Yii::app()->params['payboxKey'];
$paybox->initializeRequest(array(
    'amountCents' => 2500,
    'ccNumber' => '1111222233334444',
    'ccExpDate' => '0915',
    'cvvCode' => '123',
    'reference' => strtoupper(uniqid('', true)),
    'currency' => 'EUR',
    'countryCode' => true,
    'cardType' => true,
    //'errorCodeTest' => PayboxDirectResponse::RETURN_INVALID_CARD_NUMBER,
));

if ($paybox->authorizeAndSendPayment()) {
    $response = $paybox->getResponse();
    print_r($response->getRawData());
}
else
    throw new CException($paybox->getError());
```
