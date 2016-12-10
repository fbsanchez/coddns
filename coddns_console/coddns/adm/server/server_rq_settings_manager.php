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

$auth_level_required = get_required_auth_level('adm','server','rq_settings_manager');
$user = new CODUser();
$user->check_auth_level($auth_level_required);

function custom_die($msg) {
	echo $msg;
	echo "<script>raise_ajax_message();</script>";
	die();
}

function check_valid_conf($conf){
	// execute named-checkconf with the received content
	// if pass, backup the old conf file
	// and next update the content.

	exec ("named-checkconf " . $conf
		,$output
		,$errlevel);

	$return["out"]      = $output;
	$return["errlevel"] = $errlevel;

	return $return;

	// TODO
	// Save on /var/named/backup the conf files
	// -> allow the user restore a backuped conf file
}

$servername = secure_get("id");

if (!isset ($servername)){
	custom_die("Unauthorized to access this content.");
}

// retrieve credentials from DB

$dbclient = new DBClient($db_config);

$q = "Select * from servers where tag='" . $servername . "' ;";
$r = $dbclient->get_sql_object($q);

if (empty($r)){
	custom_die("No hay servidores registrados con ese nombre.");
}

// tried to get DB data
$serverid = $r->id;
$server_info = array();
$server_info["user"] = $r->srv_user;
$server_info["pass"] = coddns_decrypt($r->srv_password);

// also tried to get user specifications (form), if defined.
session_start();
if (   (isset($_SESSION["servers"][$servername]["user"]))
	&& (isset($_SESSION["servers"][$servername]["pass"])) ) {
	$server_info = $_SESSION["servers"][$servername];
}

if ( (! isset ($server_info["user"]) ) || (! isset($server_info["pass"])) ) {
	custom_die("No existen credenciales para acceder a este servidor.");
}
// SERVER CREDENTIALS ARE SET

require_once(__DIR__ . "/../../lib/sshclient.php");

$file_manager = array();

// initialize ssh client
$server_credentials["user"] = $server_info["user"];
$server_credentials["pass"] = $server_info["pass"];
$server_credentials["ip"]   = long2ip($r->ip);
$server_credentials["port"] = $r->port;

$sshclient = new SSHClient($server_credentials);
//	$output = $sshclient->launch("cat /etc/named.conf");

$sshclient->connect();
/**
 * Check if we're connected & authenticated into the server
 * 
 */
if (! $sshclient->is_authenticated()){
	custom_die("Datos de acceso no v&aacute;lidos");
}


$file_manager = $_SESSION["settings_manager"];

$flag = 0;
foreach ($file_manager as $item) {
	if ($_REQUEST[$item["target"]] != "") {
		$config = secure_get($item["target"], "base64");

		// Update local files 1st
		write_file($config,$item["local"]);

		// Check local files are still valid conf file
		$check = check_valid_conf($item["local"]);
		if ($check["errlevel"] === 0) {
			// Update server's files
			echo "File " . $item["remote"] . " OK!<br>";

			$r = $sshclient->send_file($item["local"], $item["remote"]);
			if (isset($r)) {
				echo "&nbsp;&nbsp;&nbsp;&nbsp;Uploaded to $servername<br>";
				$flag = 1;
			}
			else {
				echo "&nbsp;&nbsp;&nbsp;&nbsp;Uploaded failed<br>";
			}
			
		}
		else {
			echo "<b>File " . $item["remote"] . ":</b><br>";
			foreach ($check["out"] as $line) {
				$line = str_replace ($item["local"] . ":", "", $line);
				echo "&nbsp;&nbsp;&nbsp;&nbsp;$line<br>";
			}
		}				
	}
}

if ($flag == 1) {
	echo "Please restart the service<br>";
}

?>

<script>raise_ajax_message();</script>