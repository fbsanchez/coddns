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

$auth_level_required = get_required_auth_level('adm','server','new');
$user = new CODUser();
$user->check_auth_level($auth_level_required);

?>


<!Doctype HTML>

<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo $config["html_root"];?>/rs/css/pc/server_new.css" />
	
</head>

<body>
	<section>
		<h2>Add a new server</h2>

		<form name="new_server" action="#" onsubmit="fsgo('new_server', 'ajax_message','<?php echo $config["html_root"]; ?>/adm/server/server_rq_new.php', true); return false;">
			<ul>
			<li><label>IP Address/ FQDN</label><input type="text" placeholder="IP" name="ip" /></li>
			<li><label>Port</label><input type="number" placeholder="22" name="port" /></li>
			<li><label>User</label><input type="text" name="user" placeholder="user name" /></li>
			<li><label>Password</label><input type="password" name="pass" /></li>
			<li><label>Main configuration file path</label><input type="text" name="conf" value="/etc/named.conf" placeholder="/etc/named.conf" /></li>
			<li><input type="submit" name="create" value="Connect" />
		</ul>


		</form>

		<a class="return" href="<?php echo $config["html_root"] . "/?m=adm&z=center#servers" ?>">Go back</a>	
	</section>

	
</body>

</html>