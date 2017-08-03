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
<form name="edit_server" action="" method="POST" onsubmit="fsgo('edit_server', 'ajax_message','<?php echo $config["html_root"]; ?>/adm/server/server_rq_options.php', true,raise_ajax_message);return false;">
	<input type="hidden" name="op" value="edit"/>
	<input type="hidden" name="sid" value="<?php echo $server->id; ?>"/>
	<ul>
	<li><label>Server name (tag)</label><input type="text" name="tag" value="<?php echo $server->name;?>"></li>
	<li><label>IP Address/ FQDN</label><input type="text" name="ip" value="<?php echo $server->ip;?>"></li>
	<li><label>Port</label><input type="number" name="port" value="<?php echo $server->port;?>"></li>
	<li><label>User</label><input type="text" name="u" value="<?php echo $server->user;?>"></li>
	<li><label>Password</label><input type="password" name="p" value=""></li>
	<li><label>Main configuration file path</label><input type="text" name="cf" value="<?php echo $server->main_config_file;?>"></li>
	<li><label>Group</label><input type="text" name="g" value="<?php echo get_group_name($server->gid);?>"></li>
	<li><input type="submit" value="Save changes"/></li>
	</ul>
</form>

<a class="delete" href="#options" onclick="if(confirm('Are you sure?')){updateContent('ajax_message', '<?php echo $config["html_root"]; ?>/adm/server/server_rq_options.php','sn=<?php echo $servername;?>&sid=<?php echo $server->id; ?>&op=del',null,raise_ajax_message); return true;}return false;">Delete this server</a>
</body>
</html>