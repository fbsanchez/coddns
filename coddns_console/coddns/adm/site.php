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

$auth_level_required = get_required_auth_level('adm','site','');
$user = new CODUser();
$user->check_auth_level($auth_level_required);

?>


<!DOCTYPE HTML>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="rs/css/pc/adm.css">
	<!--
		<?php
			//include_once (dirname(__FILE__) . "/../rs/css/pc/adm.css");
		?>
	-->
</head>

<body>
	<?php  
		$dbclient= new DBClient($db_config);
		$dbclient->connect() or die ($dbclient->lq_error());
		
		$q = "select mail, last_login, ip_last_login from users where rol = 1;";
		$r = $dbclient->exeq($q) or die ($dbclient->lq_error());

		$q = "select mail, last_login, ip_last_login from users where rol = 2;";
		$l = $dbclient->exeq($q) or die ($dbclient->lq_error());

		$q = "select mail, last_login, ip_last_login from users where rol = 3;";
		$n = $dbclient->exeq($q) or die ($dbclient->lq_error());

		$q = "select op, auth_level from site_acl;";
		$m = $dbclient->exeq($q) or die ($dbclient->lq_error());

		$dbclient->disconnect();
	?>
	<section>
		<h2>Panel de administraci&oacute;n del sitio</h2>
		<nav>
			<a href="#" onclick = "updateContent('main_section','../adm/site.php',null,true);" title="Usuarios">
			<div class="menu_button" style="background: #FEFCFF;">
			<img src="<?php echo $config["html_root"] . "/rs/img/service_gray.png"; ?>" alt="Service Settings"/><p>Usuarios</p></div></a>

			<a href="#" onclick = "updateContent('main_section','../',null,true);" title="Acls">
			<div class="menu_button" style="background: #FEFCFF;">
			<img src="<?php echo $config["html_root"] . "/rs/img/service_gray.png"; ?>" alt="Service Settings"/><p>Acls</p></div></a>

			<a href="#" onclick = "updateContent('main_section','../',null,true);" title="Grupos">
			<div class="menu_button" style="background: #FEFCFF;">
			<img src="<?php echo $config["html_root"] . "/rs/img/service_gray.png"; ?>" alt="Service Settings"/><p>Grupos</p></div></a>
		</nav>
			<div id="hidden_div1">
				<ul>
					<li>Rol: Admin
					<!--
					<table>
						<thead>
							<tr>
								<td>Email</td>
								<td>Login</td>
								<td>Ip Login</td>
							</tr>
						</thead>
						<?php
							while ($row = $dbclient->fetch_array ($r)) {
						?>
							<tbody>
								<tr>
									<td><?php echo $row['mail'] ?></td>
									<td><?php echo $row['last_login'] ?></td>
									<td><?php echo $row['ip_last_login'] ?></td>
								</tr>
							</tbody>
						<?php  
						}
						?>
					</table>
					-->
					</li>
					<li>Rol: Manager
						<!--
						<table>
							<thead>
								<tr>
									<td>Email</td>
									<td>Login</td>
									<td>Ip Login</td>
								</tr>
							</thead>
							<?php
								while ($row = $dbclient->fetch_array ($l)) {
							?>
								<tbody>
									<tr>
										<td><?php echo $row['mail'] ?></td>
										<td><?php echo $row['last_login'] ?></td>
										<td><?php echo $row['ip_last_login'] ?></td>
									</tr>
								</tbody>
							<?php  
							}
							?>
						</table>
						-->
					</li>
					<li>Rol: Usuario
						<!--
						<table>
							<thead>
								<tr>
									<td>Email</td>
									<td>Login</td>
									<td>Ip Login</td>
								</tr>
							</thead>
							<?php
								while ($row = $dbclient->fetch_array ($n)) {
							?>
								<tbody>
									<tr>
										<td><?php echo $row['mail'] ?></td>
										<td><?php echo $row['last_login'] ?></td>
										<td><?php echo $row['ip_last_login'] ?></td>
									</tr>
								</tbody>
							<?php  
							}
							?>
						</table>
						-->
					</li>
				</ul>
			</div>

			<div id="hidden_div2">
				<table>
					<thead>
						<tr>
							<td>P&aacutegina</td>
							<td>Nivel de Aturizaci&oacuten	</td>
						</tr>
					</thead>
					<?php
						while ($row = $dbclient->fetch_array ($m)) {
					?>	
						<tbody>
							<tr>
								<td><?php echo $row['op'] ?></td>
								<td><?php echo $row['auth_level'] ?></td>
							</tr>
						</tbody>
					<?php  
					}
					?>
				</table>
			</div>

			<div id="hidden_div3">
				<p>caca</p>
			</div>
		<section id="main_section">
			
		</section>
		<!--			
		<a href="#">M&aacute;s cosas</a>
		-->
		
		<a href="<?php echo $config["html_root"] . "/?m=adm" ?>">Volver</a>
	</section>
</body>

</html>