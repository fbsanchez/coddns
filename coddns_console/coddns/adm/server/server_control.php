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

require_once(__DIR__ . "/../../include/config.php");
require_once(__DIR__ . "/../../lib/db.php");
require_once(__DIR__ . "/../../lib/util.php");
require_once(__DIR__ . "/../../lib/coduser.php");

$auth_level_required = get_required_auth_level('adm','server','control');
$user = new CODUser();
$user->check_auth_level($auth_level_required);


if(!isset($servername)){
	$servername = secure_get("id");
}
session_start();
if (!isset($_SESSION["servers"][$servername]["user"])){
	$_SESSION["servers"][$servername]["user"] = secure_get("u");
}
if (!isset($_SESSION["servers"][$servername]["pass"])){
	$_SESSION["servers"][$servername]["pass"] = secure_get("p","base64");
}

session_write_close();

?>

<div>Service control on server <?php echo $servername;?></div>