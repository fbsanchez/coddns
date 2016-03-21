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

$auth_level_required = get_required_auth_level('adm','server','manager');
$user = new CODUser();
$user->check_auth_level($auth_level_required);

$servername = secure_get("id");


$dbclient = new DBClient($db_config);
$r = $dbclient->get_sql_object("Select * from servers where tag='$servername'");

?>


<!DOCTYPE HTML>

<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo $config["html_root"];?>/rs/css/pc/tabs.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $config["html_root"];?>/rs/css/pc/service_status.css"/>

</head>

<body>
	<section>
	<h3>Administrar <i><?php echo $servername;?></i></h3>
	<h4>Informaci&oacute;n del servidor</h4>
	
	<ul style="margin: 15px 0;">
		<li>
			<label>Nombre:</label><input type="text" value="<?php echo $servername;?>"/>
		</li>
		<li>
			<label>IP:</label><input type="text" value="<?php echo long2ip($r->ip);?>"/>
		</li>
		<li>
			<label>Estado:</label><input type="text" value="<?php echo $r->status;?>"/>
		</li>
	</ul>
	
	<h4>Configuraci&oacute;n del servidor</h4>
	<pre contenteditable="true" id="gconf">
	<?php
	// Read and show main named.conf
	read_file("/etc/named.conf");
	?>
	</pre>

	<form id="update_config" method="POST" onsubmit="copyContent('gconf','gconf_input');fsgo('update_config','ajax_message','<?php echo $config["html_root"];?>/adm/server_rq_manager.php', true);return false;">
	<input id="gconf_input" type="hidden" />
	<ul>
		<li>
			<input type="submit" value="Actualizar" />
		</li>
	</ul>
	</section>
</body>

</html>
