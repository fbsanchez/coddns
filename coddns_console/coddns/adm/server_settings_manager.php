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

?>

<!DOCTYPE HTML>

<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo $config["html_root"];?>/rs/css/pc/tabs.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $config["html_root"];?>/rs/css/pc/service_status.css"/>

</head>

<body>
	<section>
	<h4>Configuraci&oacute;n del servidor</h4>
	<pre contenteditable="true" id="gconf">
	<?php
	// Read and show main named.conf
	//read_file("/etc/named.conf");
	read_file("/var/named/data/test.txt");
	?>
	</pre>

	<form id="update_config" method="POST" onsubmit="copyContent('gconf','gconf_input');fsgo('update_config','ajax_message','<?php echo $config["html_root"];?>/adm/server_rq_settings_manager.php', true);return false;">
	<input id="gconf_input" name="gconf_input" type="hidden" />
	<ul>
		<li>
			<input type="submit" value="Actualizar" />
		</li>
	</ul>
	</form>
	</section>
</body>

</html>
