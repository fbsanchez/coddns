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

$auth_level_required = get_required_auth_level('adm','servers','');
$user = new CODUser();
$user->check_auth_level($auth_level_required);

?>


<!DOCTYPE HTML>

<html>
<head>
<link rel="stylesheet" type="text/css" href="rs/css/pc/servers.css">
</head>

<body>
	<section>
	<legend>Haz click en el servidor que desees administrar:</legend>
<?php 
		/*
		<div>
			
	
			?>
			<a href="<?php echo $config["html_root"] . "/?z=adm&u=service&op=add_server";?>">
				<img class="add" src="<?php echo $config["html_root"] . "/rs/img/add.png";?>" alt="add" />
				<span>Agregar un nuevo servidor</span>
			</a>
		</div>
*/

		$dbclient = new DBClient($db_config);
		$dbclient->connect() or die($dbclient->lq_error());

		$q = "select s.id,s.tag,(select count(*) from zones z where z.server_id=s.id ) as nzones from servers s;";
		$results = $dbclient->exeq($q) or die ($dbclient->lq_error());

		while ($r = $dbclient->fetch_object($results)) {

				$named_ok = 0;
				// check named service:
				exec ("rndc status | grep running | wc -l", $out, $return);
				if (($return == 0) && ($out[0] >= 1)) { $named_ok  = 1; }

			?>

			<div class="server">

				<a id="show_<?php echo $r->tag;?>" href="<?php echo $config["html_root"];?>?z=adm&m=server&op=manager&id=<?php echo $r->tag; ?>">
				<?php 
				echo "<img src=\"";

				if ($named_ok == 1) {
					echo $config["html_root"] . "/rs/img/server_up_50.png";
					$status = "Operativo";
				}
				else {
					echo $config["html_root"] . "/rs/img/server_down_50.png";
					$status = "Desconectado";
				}
				echo "\" alt='server status'/>";
				?>
				</a>
				<div>
				<p>Servidor: <?php echo $r->tag;?></p>
				<p>Estado: <?php echo $status;?></p>
				<p>Zonas cargadas: <?php echo $r->nzones;?></p>
				</div>
			</div>
		<?php
		}

		$dbclient->disconnect();
		?>
	</section>

	<section style="margin-top: 40px;clear:both;" id="server_info">
	</section>
</body>

</html>