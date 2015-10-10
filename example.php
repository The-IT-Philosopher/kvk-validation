<html><head><meta charset=utf-8></head><body>
<?php
require_once('kvkValidation.class.php');
$APIKEY = "";

if (!(strlen($APIKEY))) die ("Get your API KEY at http://www.overheid.io");

$kvkValidation = new kvkValidation( $APIKEY);

echo "<table><tr><td><pre>";
$testKvKnummer = "56102100" ; // Beginhoven
echo "Checking for $testKvKnummer (Beginhoven)             \n";
var_export($kvkValidation->check($testKvKnummer));
echo "\nKvk Valid:";
var_export($kvkValidation->KvKvalid);
echo "\nKey Error:";
var_export($kvkValidation->KeyError);

echo "</pre></td><td><pre>";

$testKvKnummer = "64184684" ; // The IT Philosopher
echo "Checking for $testKvKnummer (The IT Philosopher)     \n";
var_export($kvkValidation->check($testKvKnummer));
echo "\nKvk Valid:";
var_export($kvkValidation->KvKvalid);
echo "\nKey Error:";
var_export($kvkValidation->KeyError);

echo "</pre></td></tr><tr><td><pre>";

$testKvKnummer = "12345678" ; // Supposed invalid
echo "Checking for $testKvKnummer (Ongeldig kvk nummer)    \n";
var_export($kvkValidation->check($testKvKnummer));
echo "\nKvk Valid:";
var_export($kvkValidation->KvKvalid);
echo "\nKey Error:";
var_export($kvkValidation->KeyError);

echo "</pre></td><td><pre>";


$kvkValidation = new kvkValidation( "blah");
$testKvKnummer = "12345678" ; // Supposed invalid
echo "Checking response when API KEY is invali             \n";
var_export($kvkValidation->check($testKvKnummer));
echo "\nKvk Valid:";
var_export($kvkValidation->KvKvalid);
echo "\nKey Error:";
var_export($kvkValidation->KeyError);

echo "</td></tr></table>";

?></body></html>
