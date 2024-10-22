<?php

/**
 * Class Config
 * houses all configurations
 */
class Config
{

    /**
     * @var string
     * Info log file
     */
    const INFO = "/var/log/applications/aggregator/INFO.log";

    /**
     * @var string
     * Debug log file
     */
    const DEBUG = "/var/log/applications/aggregator/DEBUG.log";
    /**
     * @var string
     * Error log file
     */
    const ERROR = "/var/log/applications/aggregator/DEBUG.log";

    /**
     * @var int
     * missing mandatory parameter error code
     */
    const MISSING_KEY_PARAM = 500;

    /**
     * @var int
     * success code
     */
    const SUCCESS = 200;

    /**
     * @var int
     * failed code
     */
    const FAILED = 400;

    /**
     * @var string
     * Astaan API username
     */
    const ASTAAN_USERNAME = "AstaanPay";

    /**
     * @var string
     * Astaan API password
     */
    const ASTAAN_PASSWORD = "@st@@nP#y2022==";

    /**
     * @var string
     * Astaan client ID
     */
    const ASTAAN_CLIENT_ID = 1;

    /**
     * @var string
     * Astaan URL
     */
    const ASTAAN_URL = "http://easypay.astaanmedia.com/servicerecharge.asmX";

    /**
     * @var integer
     * connect timeout in Seconds
     */
    const CONNECT_TIMEOUT = 5;

    /**
     * @var integer
     * read timeout in Seconds
     */
    const READ_TIMEOUT = 30;
    /**
     * @var string
     * Open Tembo API username
     */
    const OPENTEMBO_USERNAME = "api@opentembo.io";

    /**
     * @var string
     * Open Tembo password
     */
    const OPENTEMBO_PASSWORD = "123456";

    /**
     * @var string
     * Authentication URL
     */
    const OPENTEMBO_AUTH_URL = "https://demo.opentembo.io/simba/api/v1/gateway/authenticate";

    const OPENTEMBO_POST_PAYMENT = "https://demo.opentembo.io/simba/api/v1/gateway/postReceipt";

    const OPENTEMBO_ENQUIRY = "https://demo.opentembo.io/simba/api/v1/gateway/accountInquiry";

    const OPENTEMBO_BS_SHORTCODE = "223311";

}
