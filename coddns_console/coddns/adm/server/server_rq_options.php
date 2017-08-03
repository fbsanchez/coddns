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
require_once(__DIR__ . "/../../lib/codserver.php");
require_once(__DIR__ . "/../../include/functions_util.php");
require_once(__DIR__ . "/../../lib/coduser.php");

try {
	$auth_level_required = get_required_auth_level('adm','server','rq_options');
	$user = new CODUser();
	$user->check_auth_level($auth_level_required);
}
catch (Exception $e) {
	echo $e->getMessage();
	exit (1);
}


$servername = secure_get("sn", "url_get"); 
$option     = secure_get("op", "letters");
$sid        = secure_get("sid", "number");
$ip         = secure_get("ip", "url_get");
$user       = secure_get("u", "url_get");
$password   = secure_get("p", "password");


if ($option == "del") {
	// Delete server
	$q = "DELETE FROM servers WHERE tag = \"" . $servername . "\" AND id=" . $sid;

	$config["dbh"]->do_sql($q) or die("Cannot delete target: " . $config["dbh"]->lq_error());
	echo "Server deleted.";
}
elseif ($option == "edit") {
	// Edit server
	echo "edit server";
}


?>

<a class="ajax_button" href="<?php echo $config["html_root"] . "/?m=adm&z=center#servers"; ?>">OK</a>