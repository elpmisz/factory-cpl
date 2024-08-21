<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
require_once(__DIR__ . "/../../../vendor/autoload.php");

use App\Classes\Api;

$API = new Api();

$result = $API->preventive_read();
echo json_encode($result);
