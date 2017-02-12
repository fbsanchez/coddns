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

defined ("DEFAULT_SSH_PORT") or define("DEFAULT_SSH_PORT", 22);

require (__DIR__ . "/../../include/config.php");
require_once (__DIR__ . "/../../lib/db.php");
require_once (__DIR__ . "/../../include/functions_util.php");
require_once (__DIR__ . "/../../lib/coduser.php");
require_once (__DIR__ . "/../../lib/codserver.php");

if (! defined("_VALID_ACCESS")) { // Avoid direct access
    header ("Location: " . $config["html_root"] . "/");
    exit (1);
}

$auth_level_required = get_required_auth_level('adm','server','manager');
$user = new CODUser();
$user->check_auth_level($auth_level_required);

if(!isset($servername)){
	$servername = secure_get("id");
}

session_start();

$server = new CODServer($servername);
if ((!method_exists($server, 'get_id')) || ($server->get_id() === null)){
	echo "No hay servidores registrados con ese nombre.";
	return 0;
}

// Try to get credentials from the DB
// Priority:
//   1nd Form (form)
//   2st DB (object server)
$valid_credentials = 0;
if (empty($_SESSION["servers"][$servername]["user"])){
	if (isset ($_REQUEST["u"])) {
		$_SESSION["servers"][$servername]["user"] = secure_get("u");
		$valid_credentials++;
	}
	elseif (! empty ($server->user)) {
		$_SESSION["servers"][$servername]["user"] = $server->user;
		$valid_credentials++;
	}
}

if (empty($_SESSION["servers"][$servername]["pass"])){
	if (isset ($_REQUEST["p"])) {
		$_SESSION["servers"][$servername]["pass"] = secure_get("p","base64");
		$valid_credentials++;
	}
	elseif (! empty ($server->pass)) {
		$_SESSION["servers"][$servername]["pass"] = $server->pass;
		$valid_credentials++;
	}
}

if (empty($_SESSION["servers"][$servername]["port"])){
	if (isset ($_REQUEST["port"])) {
		$_SESSION["servers"][$servername]["port"] = secure_get("port","number");
		$valid_credentials++;
	}
	elseif (! empty ($server->port)) {
		$_SESSION["servers"][$servername]["port"] = $server->port;
		$valid_credentials++;
	}
	else {
		$_SESSION["servers"][$servername]["port"] = DEFAULT_SSH_PORT;
		$valid_credentials++;
	}
}

if (($valid_credentials == 3)
	|| (	( isset($_SESSION["servers"][$servername]["port"] ))
			&& (isset( $_SESSION["servers"][$servername]["pass"]))
			&& (isset( $_SESSION["servers"][$servername]["user"]))
	)) {    
	$server->set_credentials($_SESSION["servers"][$servername]["user"]
							,$_SESSION["servers"][$servername]["pass"]
							,$_SESSION["servers"][$servername]["port"]);
}

// should we remember the credentials?
$remember = secure_get("r","letters");
if ($remember == "on") {

	// store password for the current server
	if (!$server->save_credentials()) {
		die ("cannot store credentials...");
	}
}
if (empty($_SESSION["servers"][$servername]["forget"])){
	$forget_flag = secure_get("forget","number");
	if ($forget_flag == 1){
		// remove password stored for the current server
		$server->forgot_credentials();
		unset ($_SESSION["servers"][$servername]["user"]);
		unset ($_SESSION["servers"][$servername]["pass"]);
		unset ($_SESSION["servers"][$servername]["port"]);

	}
}

session_write_close();		

?>


<!DOCTYPE HTML>

<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo $config["html_root"];?>/rs/css/pc/tabs.css" />

</head>

<body>
<?php
$clickstatus        = "onclick=\"mark(this);updateContent('srv_content','" . $config["html_root"] . "/adm/server/server_status.php','id=" . $servername . "',true);\"";
$clickcontrol       = "onclick=\"mark(this);updateContent('srv_content','" . $config["html_root"] . "/adm/server/server_control.php','id=" . $servername . "',true);\"";
$clickconfiguration = "onclick=\"mark(this);updateContent('srv_content','" . $config["html_root"] . "/adm/server/server_settings_manager.php','id=" . $servername . "',true);\"";
$clickversioning    = "onclick=\"mark(this);updateContent('srv_content','" . $config["html_root"] . "/adm/server/server_versioning.php','id=" . $servername . "',true);\"";

?>
	<a id="status" style="display:none;"></a>
	<a id="control" style="display:none;"></a>
	<a id="settings_manager" style="display:none;"></a>
	<a id="versioning" style="display:none;"></a>
	<script type="text/javascript">
		var anchors = location.href.split('#');
		window.onload = function (){
			var tab="link_" + anchors[1];

			if (document.getElementById(tab)){
				document.getElementById(tab).onclick();
			}
		}
		function mark(id){
			document.getElementById("link_status").className="";
			document.getElementById("link_control").className="";
			document.getElementById("link_settings_manager").className="";
			document.getElementById("link_versioning").className="";
			document.getElementById("srv_content").innerHTML = "Cargando...";
			id.className = "selected";
		}
	</script>
	<section>
	<h2>Administrar <i><?php echo $servername;?></i></h2>
	<?php
	if($server->has_credentials()) {
		// Credentials had been set
		//  show navigation
	?>
	<form action="#settings_manager" method="POST">
		<input type="hidden" value="1" name="forget"/>
		<input type="submit" value="desconectar" />
	</form>

	<nav>
		<a id="link_status" href="#status" class="" <?php echo $clickstatus; ?> >
			Estado
		</a>

		<a id="link_control" href="#control" class="" <?php echo $clickcontrol; ?> >
			Control
		</a>

		<a id="link_settings_manager" href="#settings_manager" class="" <?php echo $clickconfiguration; ?> >
			Configuraci&oacute;n
		</a>
		<a id="link_versioning" href="#versioning" class="" <?php echo $clickversioning; ?> >
			Backup
		</a>
	</nav>

	<div id="srv_content" class="content">
		
	</div>

	<?php
		// End -- Allowed navigation panel
	}
	else {
		// No credentials provided to acces server.


	?>
	<form method="POST" action="#settings_manager" onsubmit="this.elements['p'].value = btoa(this.elements['p'].value);">
	<p>&gt;&gt; No se han encontrado credenciales para acceder a <?php echo $server->ip;?></p>
	<p>Indique una a continuaci&oacute;n:</p>
	<ul>
		<li>
			<label>Usuario:</label>
			<input type="text" name="u" placeholder="user"/>
		</li>
		<li>
			<label>Contrase&ntilde;a:</label>
			<input type="password" name="p" placeholder="password"/>
		</li>
		<li>
			<label>Puerto:</label>
			<input type="number" name="port" value="<?php echo $server->port;?>" />
		</li>
		<li>
			<label>Recordar contrase&ntilde;a</label><input type="checkbox" name="r"/>
		</li>
		<li>
			<input type="submit" value="Conectar"/>
		</li>
	</ul>
	</form>
	<?php
		// End -- No available credentials
	}
	?>
	<a class="return" href="<?php echo $config["html_root"];?>/?m=adm&z=center#servers">Volver</a>
	</section>
</body>

</html>
