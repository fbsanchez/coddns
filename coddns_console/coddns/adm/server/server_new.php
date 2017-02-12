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
require_once(__DIR__ . "/../../lib/util.php");
require_once(__DIR__ . "/../../lib/coduser.php");

$auth_level_required = get_required_auth_level('adm','server','new');
$user = new CODUser();
$user->check_auth_level($auth_level_required);

?>


<!Doctype HTML>

<html>
<head>
	
</head>

<body>
	<section>
		<h2>Registrar servidor</h2>

		<form action="fsgo('receiver', ); return false;" onsubmit="return false;">
			<ul>
			<li><label>Direcci&oacute;n IP/ FQDN</label><input type="text" placeholder="IP" name="ip"/></li>
			<li><label>Usuario</label><input type="text" name="user" placeholder="user name"></ins></li>
			<li><label>Contrase&ntilde;a</label><input type="password" name="pass"></ins></li>
		</ul>


		</form>
		<div id="receiver"></div>

		<a class="return" href="<?php echo $config["html_root"] . "/?m=adm&z=center#servers" ?>">Volver</a>	
	</section>

	
</body>

</html>