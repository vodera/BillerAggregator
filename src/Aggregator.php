<?php

require_once "Astaan.php";
require_once "OpenTembo.php";

/**
 * Class Aggregator
 * core class for processing all services
 */
class Aggregator
{

    public static $RESPONSE = array(
        'statusCode' => "",
        'description' => "",
        'extraData' => ""
    );

    private $astaan;
    private $utils;
    private $openTembo;


    public function __construct()
    {
        $this->astaan = new Astaan();
        $this->openTembo = new OpenTembo();
        $this->utils = new Utils();
    }

    public function processRequest($request)
    {

        try {
            $this->utils->logMessage(
                Config::INFO, 1,
                "Request received for processing: " . print_r($request, true));

            $request = json_decode($request, true);

            if (!array_key_exists("serviceCode", $request)) {
                $this->utils->logMessage(
                    Config::ERROR, 1,
                    "Request missing serviceCode");

                $response = self::$RESPONSE;
                $response['statusCode'] = Config::MISSING_KEY_PARAM;
                $response['description'] = 'Request Missing service Code';

                return json_encode($response);
            }
            $request['transactionID'] = mt_rand(100000, 999999);
            switch ($request['serviceCode']) {
                case "ASTAAN_PAY":
                    $result = $this->astaan->postPay($request);
                    if ($result) {
                        $response = self::$RESPONSE;
                        $response['statusCode'] = Config::SUCCESS;
                        $response['description'] = 'Payment processed successfully';
                    } else {
                        $response = self::$RESPONSE;
                        $response['statusCode'] = Config::FAILED;
                        $response['description'] = 'Payment processing failed';
                    }
                    break;
                case "OPENTEMBO":
                    $result = $this->openTembo->postPay($request);
                    if ($result) {
                        $response = self::$RESPONSE;
                        $response['statusCode'] = Config::SUCCESS;
                        $response['description'] = 'Payment processed successfully';
                    } else {
                        $response = self::$RESPONSE;
                        $response['statusCode'] = Config::FAILED;
                        $response['description'] = 'Payment processing failed';
                    }
                    break;
                default:
                    $response = self::$RESPONSE;
                    $response['statusCode'] = Config::MISSING_KEY_PARAM;
                    $response['description'] = 'unknown service';
                    break;
            }
        } catch (Exception $e) {
            $response = self::$RESPONSE;
            $response['statusCode'] = Config::FAILED;
            $response['description'] = 'Transaction processing failed';
        }
        return json_encode($response);
    }

}
