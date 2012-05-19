<?php
require_once 'PHPo/PHPo.php';

$po = new PHPo('test.po');

//print_r($po->getHeader());

// print_r($po->getStatements());

// print_r($po->getTranslatedStr());

print_r($po->getUnTranslatedStr());

// print_r($po->getTranslationPercentage());

// $po->getTranslatedStr();

// echo $po;
