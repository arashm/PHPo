<?php
require_once 'PHPo.php';

$po = new PHPo('gnome-color-manager.po');

print_r($po->getHeader());

print_r($po->getStatements());