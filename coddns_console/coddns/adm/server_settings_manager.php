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

require_once(dirname(__FILE__) . "/../include/config.php");
require_once(dirname(__FILE__) . "/../lib/db.php");
require_once(dirname(__FILE__) . "/../lib/util.php");
require_once(dirname(__FILE__) . "/../lib/coduser.php");

$auth_level_required = get_required_auth_level('adm','server','settings_manager');
$user = new CODUser();
$user->check_auth_level($auth_level_required);

if (!isset ($servername)){
	$servername = secure_get("id");
}
else {
	die ("Unauthorized to access this content.");
}


// retrieve credentials from DB

$dbclient = new DBClient($db_config);

$q = "Select * from servers where lower(tag)=lower('" . $servername . "');";
$r = $dbclient->get_sql_object($q);


// tried to get DB data
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
?>
	<form method="POST" action="#settings_manager" onsubmit="this.elements['p'].value = btoa(this.elements['p'].value);">
	<p>&gt;&gt; No se ha encontrado una contrase&ntilde;a para acceder a <?php echo long2ip($r->ip);?></p>
	<p>Indique una a continuaci&oacute;n:</p>
	<ul>
		<li>
			<label>Usuario:</label>
			<input type="text" name="u" placeholder="usuario"/>
		</li>
		<li>
			<label>Contrase&ntilde;a:</label>
			<input type="password" name="p" placeholder="password"/>
		</li>
		<li>
			<input type="submit" value="Conectar"/>
		</li>
	</ul>
	</form>
<?php
}
else { // SERVER CREDENTIALS ARE SET

	require_once(dirname(__FILE__) . "/../lib/sshclient.php");

	// initialize ssh client
	$server_credentials["user"] = $server_info["user"];
	$server_credentials["pass"] = $server_info["pass"];
	$server_credentials["ip"]   = long2ip($r->ip);
	
	$sshclient = new SSHClient($server_credentials);
	//	$output = $sshclient->launch("cat /etc/named.conf");

	$scp_res = $sshclient->get_file("/var/named/data/test.txt", "/tmp/prueba.txt");
	$scp_res = $sshclient->send_file("/tmp/prueba.txt", "/root/prueba.txt");

	if ($scp_res) {
		// set correct grants on remote file
		$output = $sshclient->launch("chown named:apache /root/prueba.txt; chmod 660 /root/prueba.txt");
	}
?>

	<form id="update_config" method="POST" onsubmit="copyContent('gconf','gconf_input');fsgo('update_config','ajax_message','<?php echo $config["html_root"];?>/adm/server_rq_settings_manager.php', true);return false;">
	<input id="gconf_input" name="gconf_input" type="hidden" />

	<?php echo "<p>Content of /etc/named.conf</p>"; ?>
	<textarea id="gconf" onclick="grow(this);" onkeydown="grow(this);"><?php


	$includes_array = read_file("/etc/named.conf");

	?></textarea>
	<?php

		$id=0;
		foreach ($includes_array as $fin){

			echo "<input type='hidden' name='gconf_extra_" . $id . "' value='" . $fin . "'/>";
			echo "<p>Content of " . $fin . "</p>";
			echo "<textarea id='gconf_extra_" . ($id++) . "_content'  onclick='grow(this);' onkeydown='grow(this);'>";
			array_push($includes_array, read_file($fin));
			echo "</textarea>";
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
