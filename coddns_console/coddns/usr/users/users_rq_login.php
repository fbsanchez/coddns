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

require_once (__DIR__ . "/../../include/config.php");
require_once (__DIR__ . "/../../lib/db.php");
require_once (__DIR__ . "/../../include/functions_util.php");
require_once (__DIR__ . "/../../lib/coduser.php");

try {
	$auth_level_required = get_required_auth_level('usr','users','rq_login');
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

if ($user->get_is_logged()){
    redirect($config[html_root] . "/?z=usr&m=hosts");
}

/* CASTELLANO */
$text["es"]["err1"] = "<div class='err'>Rellene todos los datos</div>";
$text["es"]["err2"] = "<div class='err'>No cumple las longitudes minimas</div>";
$text["es"]["err3"] = "<div class='err'>Los datos no son correctos.</div>";
$text["es"]["dberror"] = "<div class='err'>Woooops, contacte con el administrador del sitio.</div>";
$text["es"]["welcome"] = "<div class='ok'>Bienvenido</div><script>location='" . $config["html_root"] . "/?m=usr&z=hosts';</script></div>";

/* ENGLISH */
$text["en"]["err1"] = "<div class='err'>Please fill all data</div>";
$text["en"]["err2"] = "<div class='err'>The data provided is not reaching the minimal length";
$text["en"]["err3"] = "<div class='err'>The data providen is not valid.</div>";
$text["en"]["dberror"] = "<div class='err'>Woooops, we have a problem! please contact the site administrator.</div>";
$text["en"]["welcome"] = "<div class='ok'>Welcome</div><script>location='" . $config["html_root"] . "/?m=usr&z=hosts';</script></div>";

/* DEUTSCH */

if ( (! isset($_POST["u"])) || (! isset($_POST["p"])) ){
    echo $text[$lan]["err1"];
    exit(1);
}

$rq_pass = base64_decode($_POST["p"]);

if ( ( strlen($_POST["u"]) < MIN_USER_LENGTH) || ( strlen($rq_pass) < MIN_PASS_LENGTH) ){
    echo $text[$lan]["err2"];
    exit(2);
}

$objUser = new CODUser();
if ($objUser->login($_POST["u"], $rq_pass) == null ) {
	echo $text[$lan]["err3"];
	exit (3);
}

echo $text[$lan]["welcome"];
?>

