<?php
/* @AUTHOR: Fco de Borja Sanchez  */
/* */
include_once (dirname(__FILE__) . "/include/config.php");

session_start();
$lan=$_SESSION["lan"];


if(! isset($_SESSION["email"])){
    session_write_close();
    header ('Location: ' . $config["html_root"] . '/?lang=' . $lan);
    exit (1);
}

session_destroy();
session_write_close();

header ('Location: ' . $config["html_root"] . '/?lang=' . $lan );

?>
