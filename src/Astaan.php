<?php

require_once "utils/Utils.php";
require_once "conf/Config.php";

/**
 * Class Astaan
 * has all Astaan functions for processing Astaan requests
 */
class Astaan
{

    private $utils;

    public function __construct()
    {
        $this->utils = new Utils();
    }

    public function postPay($payload)
    {
        $date = date("dmYHis");
        $url = Config::ASTAAN_URL . '?op=postPay';
        $tokenID = MD5(Config::ASTAAN_USERNAME . '|' . Config::ASTAAN_PASSWORD . '|' . $payload['transactionID']);

        $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
<soap:Envelope xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:soap=\"http://schemas.xmlsoap.org/soap/envelope/\">
  <soap:Header>
    <ServiceAuthHeader xmlns=\"http://astaantv.com/\">
      <Username>" . Config::ASTAAN_USERNAME . "</Username>
      <Password>" . Config::ASTAAN_PASSWORD . "</Password>
    </ServiceAuthHeader>
  </soap:Header>
  <soap:Body>
    <postPay xmlns=\"http://astaantv.com/\">
      <strClientID>" . Config::ASTAAN_CLIENT_ID . "</strClientID>
      <dcAmount>" . $payload['amount'] . "</dcAmount>
      <strTranNo>" . $payload['transactionID'] . "</strTranNo>
      <strMobile>" . $payload['msisdn'] . "</strMobile>
      <dtTranDate>" . $date . "</dtTranDate>
      <strAccNo>" . $payload['accountNumber'] . "</strAccNo>
      <strTokenID>" . $tokenID . "</strTokenID>
    </postPay>
  </soap:Body>
</soap:Envelope>";

        $this->utils->logMessage(Config::DEBUG, $payload['transactionID'],
            "sending this request to Astaan | " . $xml);

        $result = $this->post($url, $xml);

        $this->utils->logMessage(Config::DEBUG, $payload['transactionID'],
            "response from Astaan | " . $result);

        $xml = simplexml_load_string(trim($result), "SimpleXMLElement", LIBXML_NOCDATA);
//        $json = json_encode($xml);
//        $arrayResult = json_decode($json, true);

        $content2 = str_replace(array_map(function ($e) {
            return "$e:";
        }, array_keys($xml->getDocNamespaces())), array(), $result);
        $xml2 = simplexml_load_string($content2);
        $this->utils->logMessage(Config::DEBUG, $payload['transactionID'],
            "array result | " . $xml2->Body->postPayResponse->postPayResult);
        if ($xml2->Body->postPayResponse->postPayResult == "Success") {
            //payment was successful
            return true;
        }

        return false;
    }

    function post($url, $payload)
    {
        try {
            // open connection
            $ch = curl_init();

            // set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: text/xml'
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_NOSIGNAL, true);
            curl_setopt($ch, CURLOPT_POST, strlen($payload));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 25);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            // execute post
            $result = curl_exec($ch);

            // check for error
            if (curl_error($ch)) {
                if (curl_errno($ch) == CURLE_OPERATION_TIMEDOUT) {
                    $this->utils->logMessage(Config::ERROR, "Astaan",
                        "NO RESPONSE from Astaan (Read timeout): ");
                } else if (curl_errno($ch) == CURLE_COULDNT_CONNECT) {
                    $this->utils->logMessage(Config::ERROR, "Astaan",
                        "COULD NOT CONNECT to Astaan (Connect timeout): ");
                } else {
                    $this->utils->logMessage(Config::ERROR, "Astaan",
                        "curl error on Astaan Payment : " . curl_error($ch));
                }

                // close connection
                curl_close($ch);
                return false;
            }

            // close connection
            curl_close($ch);
            return $result;
        } catch (Exception $e) {
            $this->utils->logMessage(Config::ERROR, "Astaan",
                "Exception occurred on Astaan request: " . $e->getMessage());
            return false;
        }
    }

}