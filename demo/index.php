<?php

require '../class.DOM_Extractor.php';

$html = file_get_contents('demo.html');
$rules = file_get_contents('demo-rules.json');

$extractor = new DOM_Extractor;
$extractor->setRules($rules)->load($html);

var_dump($extractor->parse());

?>