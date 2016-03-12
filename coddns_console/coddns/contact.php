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

require_once(dirname(__FILE__) . "/include/config.php");
require_once(dirname(__FILE__) . "/lib/db.php");
require_once(dirname(__FILE__) . "/lib/util.php");
require_once(dirname(__FILE__) . "/lib/coduser.php");

if (! defined("_VALID_ACCESS")) { // Avoid direct access
	header ("Location: " . $config["html_root"] . "/");
	exit (1);
}

$auth_level_required = get_required_auth_level('','contact','');
$user = new CODUser();
$user->check_auth_level($auth_level_required);


?>

<!DOCTYPE HTML>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="rs/css/pc/contact.css">


</head>

<body>
	<section>
		<h2>Enviar un mensaje a los desarrolladores</h2>
		<form action="#" method="POST">
			<ul>
				<li>
					<label>Nombre</label><input name="name" type="text"/>
				</li>
				<li>
					<label>Email</label><input name="email" type="email"/>
				</li>
				<li>
					<label>Telefono</label><input name="tel" type="text" pattern="(\+[0-9]{2}){0,1}[0-9]{9}" title="Introduce un n&uacute;mero de tel&eacute;fono v&aacute;lido"/>
				</li>
			</ul>
			<textarea name="mesage"></textarea>
			<ul>
				<li>
					<input type="submit" value="Enviar" />
				</li>
			</ul>
		</form>

	</section>
</body>
</html>