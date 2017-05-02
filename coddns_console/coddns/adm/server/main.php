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
require_once(__DIR__ . "/../../include/functions_server.php");
require_once(__DIR__ . "/../../lib/coduser.php");

try {
	$auth_level_required = get_required_auth_level('adm','servers','');
	$user = new CODUser();
	$user->check_auth_level($auth_level_required);
}
catch (Exception $e) {
	echo $e->getMessage();
	exit (1);
}

?>


<!DOCTYPE HTML>

<html>
<head>
<link rel="stylesheet" type="text/css" href="rs/css/pc/servers.css">
<script type="text/javascript">
	function load_servers() {
		updateContent("servers_list", "ajax.php", "action=list_servers&args=");
	}
	document.onload = load_servers();
</script>
</head>

<body>
	<section>
	<p>Please select the server you want to manage:</p>
	<br />
	<a href="<?php echo $config["html_root"];?>?m=adm&z=server&op=new">
		<img class="add" src="<?php echo $config["html_root"] . "/rs/img/add.png";?>" alt="add" />
		<span>Add new server connection</span>
	</a>
	<br />

	<section id="servers_list">
	<?php
		echo "<img src='" . $config["html_root"] . "/rs/img/loading.gif' style='width: 10px; margin: 0 15px;'/> Loading...";
	?>
	</section>
	</section>

	<section style="margin-top: 40px;clear:both;" id="server_info">
	</section>
</body>

</html>