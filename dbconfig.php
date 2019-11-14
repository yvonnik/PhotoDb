<?php

require "Smarty/libs/Smarty.class.php";
$smarty = new Smarty;
$smarty->compile_check = true;
$smarty->debugging = false;
$basedir = dirname(__FILE__) .'/';

$sys=php_uname();
if (stristr($sys,"windows")) {$unix=0;$windows=1;} else {$unix=1;$windows=0;}

if ($windows)
 {
  $Server="localhost";
  $User="photodb";
  $PW="photodb";
  $DB="PhotoDb";
  $Connection_Type="mysqli";
 }
 if ($unix)
 {
  $Server="localhost";
  $User="photodb";
  $PW="photodb";
  $DB="PhotoDb";
  $Connection_Type="mysqli";
 }
 


include_once("ADODb/adodb.inc.php");

$smarty->template_dir = $basedir . "templates";
$smarty->compile_dir=$basedir."templates_c";

/* ouverture de la bdd */

$bdd=&ADONewConnection($Connection_Type);
if (!$bdd->Connect($Server,$User,$PW,$DB)) {die("Cannot open database $Server,$User,$PW,$DB,$Connection_Type");}
$bdd->SetFetchMode(ADODB_FETCH_ASSOC);




?>
