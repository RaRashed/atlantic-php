<?php

namespace Atlantic\AtlanticGateway;

use Exception;

class Atlantic
{
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Generate a unique transaction reference.
     *
     * @return string
     */
    private function generateTransactionRef()
    {
        return vsprintf(
            '%s%s-%s-4000-8%.3s-%s%s%s0',
            str_split(dechex(microtime(true) * 1000) . bin2hex(random_bytes(8)), 4)
        );
    }

    /**
     * Make a transaction request.
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function makeTransaction(array $data)
    {
        $transactionRef = $this->generateTransactionRef();
        $data['transaction_id'] = $transactionRef;

        $url = $this->config['mode'] == 'test'
            ? "https://staging.ptranz.com/api/spi/sale"
            : "https://gateway.ptranz.com/api/spi/sale";

        $host = $this->config['mode'] == 'test'
            ? 'staging.ptranz.com'
            : 'gateway.ptranz.com';

        $headers = [
            "Accept: application/json",
            "PowerTranz-PowerTranzId: " . $this->config['transaction_id'],
            "PowerTranz-PowerTranzPassword: " . $this->config['transaction_password'],
            "Content-Type: application/json; charset=utf-8",
            "Host: " . $host,
            "Expect: 100-continue",
            "Connection: Keep-Alive",
        ];

        $payerInformation = json_decode($data['payer_information'], true);

        $requestBody = [
            "TransactionIdentifier" => $data['transaction_id'],
            "TotalAmount" => (string)$data['payment_amount'],
            "CurrencyCode" => $this->config['currency'], // e.g., 840 for USD
            "ThreeDSecure" => true,
            "FraudCheck" => true,
            "Source" => [],
            "OrderIdentifier" => $this->generateTransactionRef(),
            "BillingAddress" => [
                "FirstName" => $payerInformation['name'],
                "LastName" => $payerInformation['name'],
                "Line1" => isset($payerInformation['Line1']) ? $payerInformation['Line1'] : "1200 Whitewall Blvd.",
                "Line2" => isset($payerInformation['Line2']) ? $payerInformation['Line2'] : "Unit 15",
                "City" => isset($payerInformation['City']) ? $payerInformation['City'] : "Boston",
                "State" => isset($payerInformation['State']) ? $payerInformation['State'] : "NY",
                "PostalCode" => isset($payerInformation['PostalCode']) ? $payerInformation['PostalCode'] : "200341",
                "CountryCode" => isset($payerInformation['CountryCode']) ? $payerInformation['CountryCode'] : "840",
                "EmailAddress" => $payerInformation['email'],
                "PhoneNumber" => $payerInformation['phone'],
            ],
            "AddressMatch" => false,
            "ExtendedData" => [
                "ThreeDSecure" => [
                    "ChallengeWindowSize" => 5,
                    "ChallengeIndicator" => "01",
                ],
                "MerchantResponseUrl" => $data['callback'],
                "HostedPage" => [
                    "PageSet" => $this->config['pageset'],
                    "PageName" => $this->config['pagename'],
                ],
            ],
        ];

        return $this->sendRequest($url, $headers, $requestBody);
    }

    /**
     * Send HTTP POST Request.
     *
     * @param string $url
     * @param array $headers
     * @param array $body
     * @return array
     * @throws Exception
     */
    private function sendRequest(string $url, array $headers, array $body)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $responseData = json_decode($response, true);

        if ($httpCode !== 200) {
            throw new Exception('Error: ' . ($responseData['message'] ?? 'Unknown error'));
        }

        return $responseData;
    }
    public function checkTransaction($spiToken)
    {
        $url = $this->config['mode'] == 'test' ? "https://staging.ptranz.com/Api/spi/Payment" : "https://gateway.ptranz.com/Api/spi/Payment";
        $headers = array(
            "Accept: application/json",
            "Content-Type: application/json; charset=utf-8",
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($spiToken));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        curl_close($ch);
        $responseData = json_decode($response, true);
        return $responseData;
    }
}
