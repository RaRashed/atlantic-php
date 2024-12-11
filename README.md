# Atlantic Gateway

Atlantic Gateway is a PHP library that provides an interface to integrate payment transactions with the Atlantic Payment Gateway.  

## Features  

- Easy integration with Atlantic Payment Gateway.  
- Supports `test` and `live` modes.  
- Handles payment transactions securely.  
- Allows flexible configuration for payer information.  

## Installation  

You can install this package using Composer:  

```bash  
composer require atlantic/atlantic-gateway
```
#Configuration

Set up the gateway configuration:
```bash
$config = [  
    'mode' => 'test', // or 'live'  
    'transaction_id' => 'gateway-transaction-id',  
    'transaction_password' => 'gateway-transaction-password',  
    'currency' => '840', // USD  
    'pageset' => 'pageset',  
    'pagename' => 'pageset',  
];  

$atlantic = new Atlantic($config);
```
#Usage

Making a Transaction

You can initiate a transaction by providing the payment amount, payer information, and a callback URL:

```bash
$data = [  
    'payment_amount' => 100,  
    'payer_information' => json_encode([  
        'name' => 'Ra Rashed',  
        'email' => 'rnrashedrn@gmail.com',  
        'phone' => '+8801827801715',  
        // Optional  
        // "Line1" => "1200 Whitewall Blvd.",  
        // "Line2" => "Unit 15",  
        // "City" => "Boston",  
        // "State" => "NY",  
        // "PostalCode" => "200341",  
        // "CountryCode" => "840",  
    ]),  
    'callback' => 'return_url',  
];  

try {  
    $response = $atlantic->makeTransaction($data);  
    if (isset($response['RedirectData'])) {  
        echo $response['RedirectData'];  
    }  
} catch (Exception $e) {  
    return $e->getMessage();  
}  

```
#Checking a Transaction

You can check the status of a transaction using its SpiToken:
you can get SpiToken in your callback
```bash

$transactionCheck = $atlantic->checkTransaction($SpiToken);  

print_r($transactionCheck); 
