<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Europe/Budapest');

require '../class.DOM_Extractor.php';

$html = file_get_contents('demo.html');
$rules = file_get_contents('demo-rules.json');

$extractor = new DOM_Extractor;
$extractor->setRules($rules)->load($html);

var_dump($extractor->parse());

?>