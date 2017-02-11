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
 * <date>2016-03-24</date>
 * <update>2016-03-24</udate>
 * <summary> </summary>
 *
 *
 * Class SSHClient is based in SSH2 PHP Library
 * Allows to connect to a remote machine and execute
 * commands.
 *
 *
 * Requires php-pecl-ssh2
 * yum --enablerepo=remi install php-pecl-ssh2
 */

require_once (__DIR__ . "/../lib/codserver.php");

function get_server_data($db_config, $servername) {
	// retrieve credentials from DB

	$dbclient = new DBClient($db_config);

	$q = "Select * from servers where tag='" . $servername . "' ;";
	$server = $dbclient->get_sql_object($q);

	if (empty($server)){
		custom_die("No hay servidores registrados con ese nombre.");
	}


	// tried to get DB data
	$server_info = array();
	$server_info["user"] = $server->srv_user;
	$server_info["pass"] = coddns_decrypt($server->srv_password);

	// also tried to get user specifications (form), if defined.
	session_start();
	if (   (isset($_SESSION["servers"][$servername]["user"]))
		&& (isset($_SESSION["servers"][$servername]["pass"])) ) {
		$server_info = $_SESSION["servers"][$servername];
	}

	if ( (! isset ($server_info["user"]) ) || (! isset($server_info["pass"])) ) {
		return false;
	}

	// SERVER CREDENTIALS ARE SET
	// transform fields
	$server->user = $server_info["user"];
	$server->pass = $server_info["pass"];
	$server->ip   = long2ip($server->ip);


	return $server;
}



function transfer_conf_files($config, $sshclient, $serverid, $remote_configfile){
	// get folder name
	$localfile   = $config["spooldir"] . $serverid . $remote_configfile;
	$localfolder = dirname($localfile);

	// create local folder
	if (!is_dir($localfolder)) {
		mkdir ($localfolder, 0770,true);
	}

	// retrieve remote file
	if ($sshclient->get_file($remote_configfile, $localfile)) {
		//error_log("File " . $remote_configfile . " copied to " . $localfolder);
		return $localfile;
	}
	else {
		error_log("Failed to copy file " . $remote_configfile . " copied to " . $localfolder);
	}
	return undef;
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




?>