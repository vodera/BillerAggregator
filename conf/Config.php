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
}
