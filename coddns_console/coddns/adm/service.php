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

$auth_level_required = get_required_auth_level('adm','service','');
$user = new CODUser();
$user->check_auth_level($auth_level_required);

?>


<!DOCTYPE HTML>

<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo $config["html_root"];?>/rs/css/pc/tabs.css">
<link rel="stylesheet" type="text/css" href="<?php echo $config["html_root"];?>/rs/css/pc/adm_service.css">
<script type="text/javascript">
	var anchors = location.href.split('#');
	window.onload = function (){
		if (document.getElementById(anchors[1])){
			document.getElementById(anchors[1]).onclick();
		}
	}
	function mark(id){
		document.getElementById("service").className="";
		document.getElementById("servers").className="";
		document.getElementById("zones").className="";
		id.className = "selected";
	}
</script>
</head>

<body>
	<section>
		<h2>Centro de administraci&oacute;n</h2>

		<nav>
			<a id="service" href="#service" class="" onclick="mark(this);updateContent('adm_content','<?php echo $config["html_root"] . "/adm/service_status.php"?>');">
				Servicio
			</a>

			<a id="servers" href="#servers" class="" onclick="mark(this);updateContent('adm_content','<?php echo $config["html_root"] . "/adm/servers.php"?>');">
				Servidores
			</a>

			<a id="zones" href="#zones" class="" onclick="mark(this);updateContent('adm_content','<?php echo $config["html_root"] . "/adm/zones.php"?>');">
				Zonas
			</a>
		</nav>

		<div id="adm_content" class="content">
		</div>

		<a class="return" href="<?php echo $config["html_root"] . "/?m=adm" ?>">Volver</a>
	</section>
</body>

</html>