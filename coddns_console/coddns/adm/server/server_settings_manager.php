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

$auth_level_required = get_required_auth_level('adm','server','settings_manager');
$user = new CODUser();
$user->check_auth_level($auth_level_required);

if (!isset ($servername)){
	$servername = secure_get("id");
}
else {
	die ("Unauthorized to access this content.");
}


require_once(__DIR__ . "/../../include/functions_server.php");
require_once(__DIR__ . "/../../lib/sshclient.php");

$file_manager = array();

// Retrieve server credentials
$server = get_server_data($db_config, $servername);

?>

<!DOCTYPE HTML>

<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo $config["html_root"];?>/rs/css/pc/tabs.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $config["html_root"];?>/rs/css/pc/settings_manager.css"/>

</head>

<body>
	<section>
	<h4>Configuraci&oacute;n del servidor: <i><?php echo $server->tag;?></i></h4>


<?php
if ( $server === false ) {
	echo "No existen credenciales para acceder a este servidor.";
	return 0;
}
else { // SERVER CREDENTIALS ARE SET

	require_once(__DIR__ . "/../../lib/sshclient.php");

	$file_manager = array();

	// initialize ssh client
	$sshclient = new SSHClient($server);

	$sshclient->connect();

	// Check if we're connected & authenticated into the server
	if (! $sshclient->is_authenticated()){
		echo "Datos de acceso no v&aacute;lidos";
		return 0;
	}


	// retrieve stored configuration from remote server
	$localfile = transfer_conf_files($config, $sshclient, $server->id, $server->main_config_file);

	$id=0;
	$file_manager[$id]["local"]  = $localfile;
	$file_manager[$id]["remote"] = $server->main_config_file;
	$file_manager[$id]["target"] = "gconf_input_" . $id;

	// load files
?>

	<form id="update_config" method="POST" onsubmit="fsgo('update_config','ajax_message','<?php echo $config["html_root"];?>/adm/server/server_rq_settings_manager.php', true);return false;">
	<input name="id" value="<?php echo $servername;?>" type="hidden" />
	<input id="gconf_input_<?php echo $id;?>" name="gconf_input_<?php echo $id;?>" type="hidden" />

	<?php echo "<p>Content of " . $server->main_config_file . "</p>"; ?>
	<textarea id="gconf_<?php echo $id;?>" onclick="grow(this);" onkeydown="grow(this);" onchange="copyContent('gconf_<?php echo $id;?>','gconf_input_<?php echo $id;?>');"><?php


	$includes_array = read_file($localfile);

	?></textarea>
	<?php
	$id++;
	foreach ($includes_array as $fin){
		$local_fin = transfer_conf_files($config, $sshclient, $server->id, $fin);

		if (isset($local_fin)) {
			$file_manager[$id]["local"]  = $local_fin;
			$file_manager[$id]["remote"] = $fin;
			$file_manager[$id]["target"] = "gconf_input_" . $id;

			echo "<input type='hidden' name='gconf_input_" . $id . "' id='gconf_input_" . $id . "' />";
			echo "<p>Content of " . $fin . "</p>";
			echo "<textarea id='gconf_" . $id . "'  onclick='grow(this);' onkeydown='grow(this);' onchange=\"copyContent('gconf_" . $id . "','gconf_input_" . $id . "');\">";
			$id++;
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
	$_SESSION["settings_manager"] = $file_manager;
}
session_write_close();
?>
	</section>
</body>

</html>
