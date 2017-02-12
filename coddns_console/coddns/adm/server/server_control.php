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

$auth_level_required = get_required_auth_level('adm','server','control');
$user = new CODUser();
$user->check_auth_level($auth_level_required);

if (!isset ($servername)){
	$servername = secure_get("id");
}
else {
	die ("Unauthorized to access this content.");
}

?>


<!DOCTYPE HTML>

<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo $config["html_root"] . "/rs/css/pc/service_control.css";?>" />

</head>

<body>
	<section>
		<h3>Control del servicio</h3>

		<ul>
			<li>
				<div id="check_conf"></div>
				<a class="action" 
				   href="#control"
				   onclick="document.getElementById('check_conf').html='Cargando...'; updateContent('check_conf', '<?php echo $config["html_root"];?>/ajax.php', 'action=checkconf&id=<?php echo $servername;?>');">
				   Check configuration
				</a>
			</li>
		</ul>

		
		<div id="check_conf"></div>
		</section>
</body>

</html>
