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
 * <date>2016-03-01</date>
 * <update>2016-03-01</udate>
 * <summary> </summary>
 */

require_once (__DIR__ . "/include/config.php");
require_once (__DIR__ . "/lib/db.php");
require_once (__DIR__ . "/lib/htmlwriter.php");
require_once (__DIR__ . "/include/functions_util.php");
require_once (__DIR__ . "/lib/coduser.php");


// Allow direct access with login in POST data


$auth_token = secure_get("auth", "base64");

try {
	$auth_level_required = get_required_auth_level('','api','');
	$user = new CODUser($auth_token);
	$user->check_auth_level($auth_level_required);
} 
catch (Exception $e) {
	echo json_encode(array ("Error" => $e->getMessage()));
	exit (0);
}


// Access to main api section granted.



/**
 * Returns a data dump in JSON format with the data of the desired
 * stats_item
 */
function get_data($auth_token, $arguments) {
	global $config;
	try {
		$auth_level_required = get_required_auth_level('','api','');
		$user = new CODUser($auth_token);
		$user->check_auth_level($auth_level_required);
	} 
	catch (Exception $e) {
		echo json_encode(array ("Error" => $e->getMessage()));
		exit (0);
	}

	/*
	$arguments["oid"];
	$arguments["tstart"];
	$arguments["tend"];
	*/

	$q = " select * from stats_data where id_item=468 order by utimestamp desc limit 50";

	$result_set = $config["dbh"]->get_sql_all_objects($q);

	echo json_encode($result_set["data"]);



}



//
// AJAX API Control
//

$action    = secure_get("action");
$arguments = secure_get("args","json");

switch ($action) {
	case 'get_data':
		get_data($auth_token, $arguments);
		break;
	default:
		print json_encode(array ("Error" => "Unknown action"));
		break;
}





?>

