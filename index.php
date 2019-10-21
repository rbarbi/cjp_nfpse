<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST');

include_once('./lib/gama/base/Main.php');

$app = MainGama::getInstanceOf();

echo $app->exec($_GET, $_POST);