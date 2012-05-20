<?php
require_once 'PHPo/PHPo.php';

$po = new PHPo('test.po');

/** 
 * get header
 */
// print_r($po->getHeader());

/** 
 * Edit Header comments
 */
$header = $po->getHeader();
$po->editHeaderComment($header, "3", "ArashM");
print_r($po->getHeader());

/** 
 * Edit Header Attributes
 */
// $header = $po->getHeader();
// $po->editHeaderAttribute($header, "Project-Id-Version", "123");
// print_r($po->getHeader());


// print_r($po->getStatements());

// print_r($po->getStatements("fuzzy"));

// print_r($po->getTranslationPercentage());

// echo ($po->getStatementsByCount("untranslated"));

// print_r($po->getStatementsById(2));

// echo $po;
