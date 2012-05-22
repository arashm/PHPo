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
// $po->editHeaderCommentById("3", "ArashM");
// print_r($po->getHeader());

/** 
 * Edit Header Attributes
 */
// $po->editHeaderAttribute("Project-Id-Version", "1.2.3");
// print_r($po->getHeader());


// print_r($po->getStatements());

// print_r($po->getStatements("fuzzy"));

// print_r($po->getTranslationPercentage());

// echo ($po->getStatementsCount("untranslated"));

// print_r($po->getStatementById(2));

// Toggle a fuzzy statement
// $statement = $po->getStatementById(1);
// $po->toggleFuzzy($statement);
// print_r($po->getStatementById(1));

$comment = "# gnome-color-manager.
			# Copyright (C) 2010 gnome-color-manager's COPYRIGHT HOLDER
			# distributed under the same license as the gnome-color-manager package.
			# narcissus <3006nol@gmail.com>, 2010.
			# Arash <3006nol@gmail.com>, 2010.";

$po->editHeaderComment($comment);

print_r($po->getHeader());

// echo $po;