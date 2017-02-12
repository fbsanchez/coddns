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

$auth_level_required = get_required_auth_level('adm','server','control');
$user = new CODUser();
$user->check_auth_level($auth_level_required);

if (!isset ($servername)){
	$servername = secure_get("id");
}
else {
	die ("Unauthorized to access this content.");
}

?>


<!DOCTYPE HTML>

<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo $config["html_root"] . "/rs/css/pc/server_control.css";?>" />


<script type="text/javascript">
	function check_configuration() {
		show('check_conf');
		updateContent('check_conf', '<?php echo $config["html_root"];?>/ajax.php', 'action=checkconf&id=<?php echo $servername;?>');
	}

	function restart_service() {
		if (confirm("Are you sure?") == true) {
			show('service_report');
			updateContent('service_report', '<?php echo $config["html_root"];?>/ajax.php', 'action=restart_service&id=<?php echo $servername;?>');
		}
	}

	function show(id) {
		document.getElementById(id).style["max-height"] = "10000px";
	}

	document.onload= check_configuration();
</script>
</head>

<body>
	<section>
		<h3>Control del servicio</h3>
		<p class="action" onclick="check_configuration();">Comprobar configuraci&oacute;n</p>
		<div class="resultset" style="max-height: 0px;" id="check_conf">
			<img src="<?php echo $config['html_root']; ?>/rs/img/loading.gif" style='width: 10px; margin: 0 15px;'/>
		</div>
		

		<br />
		<p class="action" onclick="restart_service();">Reiniciar el servicio</p>

		<div class="resultset" style="max-height: 0px;" id="service_report">
			<img src="<?php echo $config['html_root']; ?>/rs/img/loading.gif" style='width: 10px; margin: 0 15px;'/>
		</div>
		</section>
</body>

</html>
