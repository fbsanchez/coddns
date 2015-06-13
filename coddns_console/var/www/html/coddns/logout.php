<?php
/* @AUTHOR: Fco de Borja Sanchez  */
/* */
session_start();
$lan=$_SESSION["lan"];


if(! isset($_SESSION["email"])){
    session_write_close();
    header ('Location: /?lang=' . $lan);
    exit (1);
}

session_destroy();
session_write_close();

header ('Location: /?lang=' . $lan );

?>
