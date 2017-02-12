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

require_once(__DIR__ . "/../include/config.php");
require_once(__DIR__ . "/../lib/db.php");
require_once(__DIR__ . "/../include/functions_util.php");
require_once(__DIR__ . "/../lib/coduser.php");

if (! defined("_VALID_ACCESS")) { // Avoid direct access
    header ("Location: " . $config["html_root"] . "/");
    exit (1);
}

$auth_level_required = get_required_auth_level('adm','','');
$user = new CODUser();
$user->check_auth_level($auth_level_required);


?>


<!DOCTYPE HTML>

<html>
<head>
<link rel="stylesheet" type="text/css" href="rs/css/pc/adm.css">
</head>

<body>
	<section>
		<h2>Panel de administraci&oacute;n</h2>
	</section>
	<nav>
		<a style="color:#2D2D2D;" href="<?php echo $config["html_root"] . "/?m=adm&z=site#users"?>">
			<div class="menu_button" style="background: #FCFEFF;">
			
				<img src="<?php echo $config["html_root"] . "/rs/img/site_options2_gray.png"; ?>" alt="Site Settings"/>
				<p>Configurar el sitio</p>
			</div>
		</a>

		<a style="color:#2D2D2D;" href="<?php echo $config["html_root"] . "/?m=adm&z=center#servers"?>">
			<div class="menu_button" style="background: #FEFCFF;">
				<img src="<?php echo $config["html_root"] . "/rs/img/service_gray.png"; ?>" alt="Administation Center"/>
				<p>Centro de administraci&oacute;n</p>
			</div>
		</a>
	</nav>
</body>

</html>