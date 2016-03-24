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

if ( (! isset ($password) ) || (! isset($srv_user)) ) {
	$srv_pass = $r->srv_password;
	$srv_user = $r->srv_user;
}
session_start();
if (isset($_SESSION["srv_user"])) {
	$srv_user = $_SESSION["srv_user"];
}
if (isset($_SESSION["srv_pass"])) {
	$srv_pass = $_SESSION["srv_pass"];
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
if ( (! isset ($srv_pass) ) || (! isset($srv_user)) ) {
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
	$server_credentials["user"] = $srv_user;
	$server_credentials["pass"] = $srv_pass;
	$server_credentials["ip"]   = long2ip($r->ip);
	
	$sshclient = new SSHClient($server_credentials);
	$output = $sshclient->launch("cat /etc/named.conf");

	var_dump($output);

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
