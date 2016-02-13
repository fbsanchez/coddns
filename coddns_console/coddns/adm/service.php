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

if (! defined("_VALID_ACCESS")) { // Avoid direct access
    header ("Location: " . $config["html_root"] . "/");
    exit (1);
}

$auth_level_required = get_required_auth_level('adm','service','');
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
		<h2>Administraci&oacute;n del servicio Bind</h2>

		<?php
		$named_ok = 0;
		// check named service:
		exec ("ps aux | grep named | grep -v grep | wc -l", $out, $return);
		if (($return == 0) && ($out[0] >= 1)) { $named_ok  = 1; }

		?>

		<div class="server">
			<?php

			echo "<a href='" . $config["html_root"] . "/?m=adm&z=service&op=manager&id=" . $r->tag . "'><img width=\"50px\" src=\"";
			if ($named_ok == 1) {
				echo $config["html_root"] . "/rs/img/server_up.png";
				$status = "Operativo";
			}
			else {
				echo $config["html_root"] . "/rs/img/server_down.png";
				$status = "Desconectado";
			}
			echo "\" alt='server status'/></a>";
			?>

			<div>
			<p>Servidor: <?php echo $r->tag;?></p>
			<p>Estado: <?php echo $status;?></p>
			<p>Zonas cargadas: <?php echo $r->zones;?></p>
			</div>
		</div>
		

		<a href="<?php echo $config["html_root"] . "/?m=adm" ?>">Volver</a>
	</section>
</body>

</html>