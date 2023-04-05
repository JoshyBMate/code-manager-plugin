<?php
 require_once(__DIR__ . '/vendor/autoload.php'); 
// Configure API key authorization: api-key
$config = SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', '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');
// // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// // $config = SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKeyPrefix('api-key', 'Bearer');
// // Configure API key authorization: partner-key
// $config = SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('partner-key', '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');
// // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// // $config = SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKeyPrefix('partner-key', 'Bearer');

$apiInstance = new SendinBlue\Client\Api\AccountApi(
	new GuzzleHttp\Client(),
	$config
);
