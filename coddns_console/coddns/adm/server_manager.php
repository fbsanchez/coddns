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
if (!isset($_SESSION["servers"][$servername]["user"])){
	$_SESSION["servers"][$servername]["user"] = secure_get("u");
}
if (!isset($_SESSION["servers"][$servername]["pass"])){
	$_SESSION["servers"][$servername]["pass"] = secure_get("p","base64");
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
$clickstatus        = "onclick=\"mark(this);updateContent('srv_content','" . $config["html_root"] . "/adm/server_status.php','id=" . $servername . "');\"";
$clickcontrol       = "onclick=\"mark(this);updateContent('srv_content','" . $config["html_root"] . "/adm/server_control.php','id=" . $servername . "');\"";
$clickconfiguration = "onclick=\"mark(this);updateContent('srv_content','" . $config["html_root"] . "/adm/server_settings_manager.php','id=" . $servername . "');\"";
$clickversioning    = "onclick=\"mark(this);updateContent('srv_content','" . $config["html_root"] . "/adm/server_versioning.php','id=" . $servername . "');\"";

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


	<a class="return" href="<?php echo $config["html_root"];?>/?m=adm&z=service#servers">Volver</a>
	</section>
</body>

</html>
