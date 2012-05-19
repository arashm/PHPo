<?php
require_once 'PHPo/PHPo.php';

$po = new PHPo('test.po');

//print_r($po->getHeader());

//print_r($po->getStatements());

echo $po;
