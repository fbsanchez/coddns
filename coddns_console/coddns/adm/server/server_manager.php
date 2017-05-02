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

try {
	$auth_level_required = get_required_auth_level('adm','server','manager');
	$user = new CODUser();
	$user->check_auth_level($auth_level_required);
}
catch (Exception $e) {
	echo $e->getMessage();
	exit (1);
}

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
$clickoptions       = "onclick=\"mark(this);updateContent('srv_content','" . $config["html_root"] . "/adm/server/server_options.php','id=" . $servername . "',true);\"";

?>
	<a id="status" style="display:none;"></a>
	<a id="control" style="display:none;"></a>
	<a id="settings_manager" style="display:none;"></a>
	<a id="options" style="display:none;"></a>
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
			document.getElementById("link_options").className="";
			document.getElementById("srv_content").innerHTML = '<img src="<?php echo $config['html_root']; ?>/rs/img/loading.gif" style="width: 10px; margin: 0 15px;"/>Loading...';
			id.className = "selected";
		}
	</script>
	<section>
	<h2>Manage <i><?php echo $servername;?></i></h2>
	<p class="subtitle"><?php echo $server->ip;?></p>
	<?php
	if($server->has_credentials()) {
		// Credentials had been set
		//  show navigation
	?>
	
	<nav>
		<a id="link_status" href="#status" class="" <?php echo $clickstatus; ?> >
			Status
		</a>

		<a id="link_control" href="#control" class="" <?php echo $clickcontrol; ?> >
			Manage
		</a>

		<a id="link_settings_manager" href="#settings_manager" class="" <?php echo $clickconfiguration; ?> >
			Bind configuration
		</a>
		<a id="link_options" href="#options" class="" <?php echo $clickoptions; ?> >
			Options
		</a>
	</nav>

	<div id="srv_content" class="content">
		
	</div>

	<form action="#settings_manager" method="POST" onsubmit="if(!confirm('Are you sure?')) { return false; }">
		<input type="hidden" value="1" name="forget"/>
		<input type="submit" value="desconectar" />
	</form>

	<?php
		// End -- Allowed navigation panel
	}
	else {
		// No credentials provided to acces server.


	?>
	<form method="POST" action="#settings_manager" onsubmit="this.elements['p'].value = btoa(this.elements['p'].value);">
	<p>&gt;&gt; There are no credentials set to access <?php echo $server->ip;?></p>
	<p>Please specify:</p>
	<ul>
		<li>
			<label>User</label>
			<input type="text" name="u" placeholder="user"/>
		</li>
		<li>
			<label>Password</label>
			<input type="password" name="p" placeholder="password"/>
		</li>
		<li>
			<label>Port</label>
			<input type="number" name="port" value="<?php echo $server->port;?>" />
		</li>
		<li>
			<label>Store credentials</label><input type="checkbox" name="r"/>
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
	<a class="return" href="<?php echo $config["html_root"];?>/?m=adm&z=center#servers">Go back</a>
	</section>
</body>

</html>
