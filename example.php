<html><head><meta charset=utf-8></head><body>
<?php
require_once('kvkValidation.class.php');
$APIKEY = ""; //Register your API KEY at https://www.overheid.io

$kvkValidation = new kvkValidation( $APIKEY);
$kvkValidation->check("56102100");

?></body></html>
