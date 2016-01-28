<?php
include_once (dirname(__FILE__) . "/include/config.php");
require_once (dirname(__FILE__) . "/lib/ipv4.php");

echo json_encode(_ip());

?>
