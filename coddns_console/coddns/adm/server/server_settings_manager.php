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

$auth_level_required = get_required_auth_level('adm','server','settings_manager');
$user = new CODUser();
$user->check_auth_level($auth_level_required);

if (!isset ($servername)){
	$servername = secure_get("id");
}
else {
	die ("Unauthorized to access this content.");
}


function transfer_conf_files($config, $sshclient, $serverid, $remote_configfile){
	// get folder name
	$localfile   = $config["spooldir"] . $serverid . $remote_configfile;
	$localfolder = dirname($localfile);

	// create local folder
	mkdir ($localfolder, 0770,true);

	// retrieve remote file
	if ($sshclient->get_file($remote_configfile, $localfile)) {
		error_log("File " . $remote_configfile . " copied to " . $localfolder);
		return $localfile;
	}
	else {
		error_log("Failed to copy file " . $remote_configfile . " copied to " . $localfolder);
	}
	return undef;
}

// retrieve credentials from DB

$dbclient = new DBClient($db_config);

$q = "Select * from servers where tag='" . $servername . "' ;";
$r = $dbclient->get_sql_object($q);

if (empty($r)){
	echo "No hay servidores registrados con ese nombre.";
	return 0;
}

// tried to get DB data
$serverid = $r->id;
$server_info["user"] = $r->srv_user;
$server_info["pass"] = coddns_decrypt($r->srv_password);


// also tried to get user specifications (form), if defined.
session_start();
if (   (isset($_SESSION["servers"][$servername]["user"]))
	&& (isset($_SESSION["servers"][$servername]["pass"])) ) {
	$server_info = $_SESSION["servers"][$servername];
}
session_write_close();

?>

<!DOCTYPE HTML>

<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo $config["html_root"];?>/rs/css/pc/tabs.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $config["html_root"];?>/rs/css/pc/settings_manager.css"/>

</head>

<body>
	<section>
	<h4>Configuraci&oacute;n del servidor: <i><?php echo $r->tag;?></i></h4>


<?php
if ( (! isset ($server_info["user"]) ) || (! isset($server_info["pass"])) ) {
	echo "No existen credenciales para acceder a este servidor.";
	return 0;
}
else { // SERVER CREDENTIALS ARE SET

	require_once(__DIR__ . "/../../lib/sshclient.php");

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
		echo "Datos de acceso no v&aacute;lidos";
		return 0;
	}

	$localfile = transfer_conf_files($config, $sshclient, $serverid, $r->main_config_file);

	// load files
?>

	<form id="update_config" method="POST" onsubmit="copyContent('gconf','gconf_input');fsgo('update_config','ajax_message','<?php echo $config["html_root"];?>/adm/server/server_rq_settings_manager.php', true);return false;">
	<input id="gconf_input" name="gconf_input" type="hidden" />

	<?php echo "<p>Content of " . $r->main_config_file . "</p>"; ?>
	<textarea id="gconf" onclick="grow(this);" onkeydown="grow(this);"><?php


	$includes_array = read_file($localfile);

	?></textarea>
	<?php
	$id=0;
	foreach ($includes_array as $fin){
		$local_fin = transfer_conf_files($config, $sshclient, $serverid, $fin);

		if (isset($local_fin)) {
			echo "<input type='hidden' name='gconf_extra_" . $id . "' value='" . $local_fin . "'/>";
			echo "<p>Content of " . $fin . "</p>";
			echo "<textarea id='gconf_extra_" . ($id++) . "_content'  onclick='grow(this);' onkeydown='grow(this);'>";
			array_push($includes_array, read_file($local_fin));
			echo "</textarea>";
		}
	}
	?>	
	<ul>
		<li>
			<input type="submit" value="Actualizar" />
		</li>
	</ul>
	</form>

<?php
}
?>
	</section>
</body>

</html>
