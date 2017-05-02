<?php
/**
 * <copyright company="CODDNS">
 * Copyright (c) 2013 All Right Reserved, http://coddns.es/
 *
 * THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY
 * KIND, EITHER EXPRESSED OR IMPLIED, NO INCLUDING THE WARRANTIES OF
 * MERCHANTABILITY AND/OR FITNESS FOR A PARTICULAR PURPOSE.
 *
 * </copyright>
 * <author>Fco de Borja Sanchez</author>
 * <email>fborja.sanchezs@gmail.com</email>
 * <date>2016-02-11</date>
 * <update>2016-02-11</udate>
 * <summary> </summary>
 */

require_once (__DIR__ . "/include/config.php");
require_once (__DIR__ . "/lib/db.php");
require_once (__DIR__ . "/include/functions_util.php");
require_once (__DIR__ . "/lib/coduser.php");

try {
	$auth_level_required = get_required_auth_level('','rest_host','');
	$user = new CODUser();
	$user->check_auth_level($auth_level_required);
}
catch (Exception $e) {
	echo $e->getMessage();
	exit (1);
}

session_start();
if (!isset($_SESSION["lan"])){
    $_SESSION["lan"] = "es";
}
$lan = $_SESSION["lan"];
session_write_close();

// devuelve la disponibilidad o no de una etiqueta host para un subdominio dado
if (   (! isset ($_POST["h"] ))
	|| (! isset ($_POST["z"] )) ){
    die("Unauthorized access");
}

if (   ( strlen ($_POST["h"]) < MIN_HOST_LENGTH )
    || ( strlen ($_POST["h"]) > MAX_HOST_LENGTH )
    || ( !preg_match('/^[a-zA-Z]+([0-9]*[a-zA-Z]*)*$/', $_POST["h"])) ) {
    die ("<div class='r err'>No cumple los requisitos</div>");
}

$dbclient = $config["dbh"];

$host = $dbclient->prepare($_POST["h"], "letters");
$zone = $dbclient->prepare($_POST["z"], "url_get");


$q = "select * from hosts where lower(tag)=lower('" . $host . "." . $zone . "');";
$dbclient->exeq($q);
if ( $dbclient->lq_nresults() > 0 )
	echo "<div class='r err'>No disponible</div>";
else
	echo "<div class='r ok'>Disponible</div>";
?>

