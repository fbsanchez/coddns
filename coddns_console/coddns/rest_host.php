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

require_once (dirname(__FILE__) . "/include/config.php");
require_once (dirname(__FILE__) . "/lib/db.php");
require_once (dirname(__FILE__) . "/lib/util.php");
require_once (dirname(__FILE__) . "/lib/coduser.php");

$auth_level_required = get_required_auth_level('','rest_host','');
$user = new CODUser();
$user->check_auth_level($auth_level_required);

session_start();
if (!isset($_SESSION["lan"])){
    $_SESSION["lan"] = "es";
}
$lan = $_SESSION["lan"];
session_write_close();


defined ("LENGTH_HOST_MIN") or define ("LENGTH_HOST_MIN", 1);
defined ("LENGTH_HOST_MAX") or define ("LENGTH_HOST_MAX", 200);

// devuelve la disponibilidad o no de una etiqueta host para un subdominio dado
if (! isset ($_POST["h"] )){
    die("Unauthorized access");
}

$dbclient = new DBClient($db_config);

$dbclient->connect() or die("ERR");

$host = $dbclient->prepare($_POST["h"], "letters");

if (   ( strlen ($host) < LENGTH_HOST_MIN )
    || ( strlen ($host) > LENGTH_HOST_MAX )
    || ( !preg_match('/^[a-zA-Z]+([0-9]*[a-zA-Z]*)*$/',$_POST["h"])) ) {
    die ("<div class='r err'>No cumple los requisitos</div>");
}
$q = "select * from hosts where lower(tag)=lower('" . $host . "." . $config["domainname"] ."');";
$dbclient->exeq($q);
if ( $dbclient->lq_nresults() > 0 )
        echo "<div class='r err'>No disponible</div>";
else
        echo "<div class='r ok'>Disponible</div>";
$dbclient->disconnect();
?>

