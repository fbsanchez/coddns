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
require_once(__DIR__ . "/../../include/functions_util.php");
require_once(__DIR__ . "/../../lib/coduser.php");

try {
	$auth_level_required = get_required_auth_level('adm','server','rq_settings_manager');
	$user = new CODUser();
	$user->check_auth_level($auth_level_required);
}
catch (Exception $e) {
	echo $e->getMessage();
	exit (1);
}

function custom_die($msg) {
	echo $msg;
	echo '<a class="ajax_button" href="#" onclick="location.reload();">OK</a>';
	die();
}

$servername = secure_get("id");

if (!isset ($servername)){
	custom_die("Unauthorized to access this content.");
}

require_once(__DIR__ . "/../../include/functions_server.php");
require_once(__DIR__ . "/../../lib/sshclient.php");

$file_manager = array();

// Retrieve server credentials
$server_credentials = get_server_data($db_config, $servername);

if ($server_credentials === false) {
	custom_die("No existen credenciales para acceder a este servidor.");
}

if (isset($config["session"][$servername]) && isset($config["session"][$servername]["settings_manager"])) {
        $file_manager = $config["session"][$servername]["settings_manager"];
}
else {
        custom_die("<p>Something went wrong</p>");
}

// initialize ssh client
$sshclient = new SSHClient($server_credentials);

$sshclient->connect();

// Check if we're connected & authenticated into the server
if (! $sshclient->is_authenticated()){
	custom_die("<p>Cannot connect to server.</p><p>Please check IP, port, user name, password and sshd status.</p>");
}

$flag = 0;

foreach ($file_manager as $item) {
	if ($_REQUEST[$item["target"]] != "") {
		$config = secure_get($item["target"], "base64");

		// Update local files 1st
		write_file($config,$item["local"]);

		// Copy local files to remote tmp
		if($sshclient->send_file($item["local"], $item["temp"]) === false) {
			echo "File " . $item["temp"] . " failed...<br>";
			continue;
		}

		// Check remotely if the tmp file is valid
		$check = $sshclient->check_valid_conf($item["temp"]);
		if (($check[0] == "") && ($check[1] == "")) {
			// Update server's files
			echo "File " . $item["remote"] . " OK!<br>";

			// Overwrite remote configuration
			$r = $sshclient->apply_conf($item["temp"], $item["remote"]);
			if ($r === true) {
				echo "&nbsp;&nbsp;&nbsp;&nbsp;Uploaded to $servername<br>";
				$flag = 1;
			}
			else {
				echo "&nbsp;&nbsp;&nbsp;&nbsp;Uploaded failed<br>";
			}
			
		}
		else {
			echo "<b>File " . $item["remote"] . ":</b><br>";
			foreach ($check as $line) {
				$line = str_replace ($item["local"] . ":", "", $line);
				echo "&nbsp;&nbsp;&nbsp;&nbsp;$line<br>";
			}
		}				
	}
	echo "</p>";
}

echo "</pre>";

if ($flag == 1) {
	echo "Please restart the service<br>";
	echo '<a class="ajax_button" href="#control" onclick="location.reload();">OK</a>';
}
else {
	echo '<a class="ajax_button" href="#control" onclick="close_ajax_message();">OK</a>';
}

?>
