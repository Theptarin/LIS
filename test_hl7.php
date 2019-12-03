<?php
require_once "HL7File.php";
$filename = "egfr01.hl7";
$hl7 = new HL7File($filename,'\r\n');
print_r($hl7->get_message());

