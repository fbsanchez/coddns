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


		<div>
			<?php 
			// Action pane
			?>
			<a href="<?php echo $config["html_root"] . "/?z=adm&u=service&op=add_server";?>">
				<img class="add" src="<?php echo $config["html_root"] . "/rs/img/add.png";?>" alt="add" />
				<span>Agregar un nuevo servidor</span>
			</a>
		</div>

		<?php
		$named_ok = 0;
		// check named service:
		exec ("ps aux | grep named | grep -v grep | wc -l", $out, $return);
		if (($return == 0) && ($out[0] >= 1)) { $named_ok  = 1; }

		$dbclient = new DBClient($db_config);
		$dbclient->connect() or die($dbclient->lq_error());

		$q = "select s.id,s.tag, count(z.domain) nzones from servers s, zones z where z.server_id=s.id group by s.id;";
		$results = $dbclient->exeq($q) or die ($dbclient->lq_error());

		while ($r = $dbclient->fetch_object($results)) {

			?>

			<div class="server">
				<?php

				echo "<a href='" . $config["html_root"] . "/?m=adm&z=service&op=manager&id=" . $r->tag . "'><img src=\"";
				if ($named_ok == 1) {
					echo $config["html_root"] . "/rs/img/server_up_50.png";
					$status = "Operativo";
				}
				else {
					echo $config["html_root"] . "/rs/img/server_down_50.png";
					$status = "Desconectado";
				}
				echo "\" alt='server status'/></a>";
				?>

				<div>
				<p>Servidor: <?php echo $r->tag;?></p>
				<p>Estado: <?php echo $status;?></p>
				<p>Zonas cargadas: <?php echo $r->nzones;?></p>
				</div>
			</div>
		<?php
		}
		?>			

		<a href="<?php echo $config["html_root"] . "/?m=adm" ?>">Volver</a>
	</section>
</body>

</html>