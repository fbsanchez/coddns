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

if (! defined("_VALID_ACCESS")) { // Avoid direct access
    header ("Location: " . $config["html_root"] . "/");
    exit (1);
}

$auth_level_required = get_required_auth_level('adm','center','');
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
		var tab="link_" + anchors[1];

		if (document.getElementById(tab)){
			document.getElementById(tab).onclick();
		}
	}
	function mark(id){
		document.getElementById("link_servers").className="";
		document.getElementById("link_zones").className="";
		document.getElementById("adm_content").innerHTML = '<img src="<?php $config['html_root']; ?>/rs/img/loading.gif" style="width: 10px; margin: 0 15px;"/>Cargando...';
		id.className = "selected";
	}
</script>
</head>

<body>

<?php
$clickservers = "onclick=\"mark(this);updateContent('adm_content','" . $config["html_root"] . "/adm/server/main.php');\"";
$clickzones = "onclick=\"mark(this);updateContent('adm_content','" . $config["html_root"] . "/adm/zones/zones.php');\"";

?>
	<a id="servers" style="display:none;"></a>
	<a id="zones" style="display:none;"></a>
	<section>
		<h2>Centro de control</h2>

		<nav>
			<a id="link_servers" href="#servers" class="" <?php echo $clickservers; ?> >
				Servidores
			</a>

			<a id="link_zones" href="#zones" class="" <?php echo $clickzones; ?> >
				Zonas
			</a>
		</nav>

		<div id="adm_content" class="content">
		</div>

		<a class="return" href="<?php echo $config["html_root"] . "/?m=adm" ?>">Volver</a>
	</section>
</body>

</html>