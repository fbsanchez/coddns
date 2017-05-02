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
require_once(__DIR__ . "/../../lib/codserver.php");
require_once(__DIR__ . "/../../include/functions_util.php");
require_once(__DIR__ . "/../../lib/coduser.php");

try {
	$auth_level_required = get_required_auth_level('adm','server','options');
	$user = new CODUser();
	$user->check_auth_level($auth_level_required);
}
catch (Exception $e) {
	echo $e->getMessage();
	exit (1);
}

if (!isset ($servername)){
	$servername = secure_get("id");
}
else {
	die ("Unauthorized to access this content.");
}

$server = new CODServer($servername);

?>


<!DOCTYPE HTML>

<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo $config["html_root"] . "/rs/css/pc/server_control.css";?>" />
</head>
<body>
<form>
	<ul>
	<li><label>Server name (tag)</label><input type="text" name="tag" value="<?php echo $server->name;?>"></li>
	<li><label>IP Address/ FQDN</label><input type="text" name="tag" value="<?php echo $server->ip;?>"></li>
	<li><label>Port</label><input type="number" name="tag" value="<?php echo $server->port;?>"></li>
	<li><label>User</label><input type="text" name="tag" value="<?php echo $server->user;?>"></li>
	<li><label>Password</label><input type="password" name="tag" value=""></li>
	<li><label>Main configuration file path</label><input type="text" name="tag" value="<?php echo $server->main_config_file;?>"></li>
	<li><label>Group</label><input type="text" name="tag" value="<?php echo get_group_name($server->gid);?>"></li>
	</ul>
</form>

</body>
</html>