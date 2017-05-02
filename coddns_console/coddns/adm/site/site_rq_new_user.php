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
require_once (__DIR__ . "/../../include/functions_ip.php");
require_once (__DIR__ . "/../../include/functions_util.php");
require_once (__DIR__ . "/../../lib/coduser.php");

try {
	$auth_level_required = get_required_auth_level('adm','site','rq_new_user');
	$user = new CODUser();
	$user->check_auth_level($auth_level_required);
}
catch (Exception $e) {
	echo $e->getMessage();
	exit (1);
}

$error = 0;
if (   (! isset ($_POST["email"])  )
    || (! isset ($_POST["rol"]) )
    || (! isset ($_POST["groups"]) )) {
    $error = 1;
}

var_dump($_POST);
var_dump($error);
/*
if(!$error){
	$dbclient->connect() or die ($dbclient->lq_error());
	$q = "insert into users (mail, rol) values ('" . $_POST["email"] . "', '" . $_POST["rol"] . "');";
	$dbclient->exeq($q) or die($dbclient->lq_error());
	//$r = "select id from users where mail='"$_POST["email"]"';";
	//$s = "insert into tusers_groups (gid, oid) values (aki va el bucle para que meta todos los grupos, "$dbclient->exeq($r) or die($dbclient->lq_error())");";
	//$dbclient->exeq($s) or die($dbclient->lq_error());
	$dbclient->disconnect();
}
*/
?>




