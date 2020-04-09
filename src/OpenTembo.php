<?php


require_once "utils/Utils.php";
require_once "conf/Config.php";

/**
 * Class OpenTembo
 * houses all OPENTEMBO functions
 */
class OpenTembo
{

    private $utils;

    public function __construct()
    {
        $this->utils = new Utils();
    }


    public function postEnquiry($payload){

        $token = $this->getToken();
        if (empty($token)) {
            $this->utils->logMessage(Config::ERROR, $payload['accountNumber'],
                "fetch Token failed on enquiry");
            return false;
        }

        $payload = array(
            'billRefNumber' => $payload['accountNumber'],
            'authToken' => $token,
            'businessShortCode' => Config::OPENTEMBO_BS_SHORTCODE
        );
        $this->utils->logMessage(Config::INFO, $payload['accountNumber'],
            "sending enquiry request | " . json_encode($payload));

        //send request
        $response = $this->post(Config::OPENTEMBO_ENQUIRY, $payload);

        $this->utils->logMessage(Config::INFO, $payload['accountNumber'],
            "post enquiry response | " . print_r($response, true));

        if (empty($response)) {
            $this->utils->logMessage(Config::ERROR, $payload['accountNumber'],
                "post enquiry failed");
            return false;
        }
        $response = json_decode($response, true);
        if (isset($response['error']) && $response['error'] == false) {
            return $response;
        }
        return false;

    }
    /**
     * @param $payload
     * @return bool
     * post payment to OpenTembo
     */
    public function postPay($payload)
    {
        $token = $this->getToken();
        if (empty($token)) {
            $this->utils->logMessage(Config::ERROR, $payload['accountNumber'],
                "fetch Token failed");
            return false;
        }

        $date = date("YHis");
        $payload = array(
            'billRefNumber' => $payload['accountNumber'],
            'authToken' => $token,
            'businessShortCode' => Config::OPENTEMBO_BS_SHORTCODE,
            'transID' => $payload['transactionID'],
            'transAmount' => $payload['amount'],
            'transTime' => $date,
            'msisdn' => $payload['msisdn'],
            'name' => $payload['customerName']
        );
        $this->utils->logMessage(Config::INFO, $payload['accountNumber'],
            "sending post payment request | " . json_encode($payload));

        //send request
        $response = $this->post(Config::OPENTEMBO_POST_PAYMENT, $payload);

        $this->utils->logMessage(Config::INFO, $payload['accountNumber'],
            "post payment response | " . print_r($response, true));

        if (empty($response)) {
            $this->utils->logMessage(Config::ERROR, $payload['accountNumber'],
                "post payment failed");
            return false;
        }
        $response = json_decode($response, true);
        if (isset($response['error']) && $response['error'] == false) {
            return true;
        }
        return false;
    }

    private function getToken()
    {

        try {
            $payload = array(
                'username' => Config::OPENTEMBO_USERNAME,
                'password' => Config::OPENTEMBO_PASSWORD
            );
            $this->utils->logMessage(Config::INFO, $payload['accountNumber'],
                "sending get token request | " . json_encode($payload));

            //send request
            $response = $this->post(Config::OPENTEMBO_AUTH_URL, $payload);

            if ($response) {
                $response = json_decode($response, true);
                if (isset($response['authToken']) && !empty($response['authToken']))
                    return $response['authToken'];

            } else {
                $this->utils->logMessage(Config::ERROR, "OpenTembo",
                    "fetch Token failed");
            }
        } catch (Exception $e) {
            $this->utils->logMessage(Config::ERROR, "OpenTembo",
                "Exception occurred on get token: " . $e->getMessage());
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
                'Content-Type: application/json',
                'Connection: Keep-Alive'
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_NOSIGNAL, true);
            curl_setopt($ch, CURLOPT_POST, count($payload));
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, Config::CONNECT_TIMEOUT);
            curl_setopt($ch, CURLOPT_TIMEOUT, Config::READ_TIMEOUT);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            // execute post
            $result = curl_exec($ch);

            // check for error
            if (curl_error($ch)) {
                if (curl_errno($ch) == CURLE_OPERATION_TIMEDOUT) {
                    $this->utils->logMessage(Config::ERROR, "OpenTembo",
                        "NO RESPONSE from OpenTembo (Read timeout): ");
                    $result = -CURLE_OPERATION_TIMEDOUT;
                } else {
                    if (curl_errno($ch) == CURLE_COULDNT_CONNECT) {
                        $this->utils->logMessage(Config::ERROR, "OpenTembo",
                            "COULD NOT CONNECT to OpenTembo (Connect timeout): ");
//                        $result = -CURLE_COULDNT_CONNECT;
                    } else {
                        $this->utils->logMessage(Config::ERROR, "OpenTembo",
                            "curl error on OpenTembo connection : " . curl_error($ch));
                    }
                }

                // close connection
                curl_close($ch);
                return false;
            }

            // close connection
            curl_close($ch);
            return $result;
        } catch (Exception $e) {
            $this->utils->logMessage(Config::ERROR, "OpenTembo",
                "Exception occurred on OpenTembo: " . $e->getMessage());
            return false;
        }
    }
}
