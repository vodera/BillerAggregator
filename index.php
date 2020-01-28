<?php

require_once "src/Aggregator.php";
require_once "conf/Config.php";

$data = file_get_contents('php://input');

$processor = new Aggregator();
echo $processor->processRequest($data);