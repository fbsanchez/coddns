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


?>

<!DOCTYPE HTML>

<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo $config["html_root"];?>/rs/css/pc/tabs.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $config["html_root"];?>/rs/css/pc/settings_manager.css"/>

</head>

<body>
	<section>
	<h4>Configuraci&oacute;n del servidor</h4>
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
	</section>
</body>

</html>
