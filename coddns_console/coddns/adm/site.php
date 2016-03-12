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
	<link rel="stylesheet" type="text/css" href="rs/css/pc/adm_site.css">
</head>
<script languague="javascript">
        function show(id) {
        	document.getElementById('hidden_div1').style.display = '';
        	document.getElementById('hidden_div2').style.display = '';
        	document.getElementById('content_group').style.display = '';
            document.getElementById(id).style.display = "block";
        }
</script>
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
			<a href="javascript:show('hidden_div1');" title="Usuarios">
			<div class="menu_button" style="background: #FEFCFF;">
			<img src="<?php echo $config["html_root"] . "/rs/img/teamwork-in-the-office.png"; ?>" alt="Service Settings"/><p>Usuarios</p></div></a>

			<a href="javascript:updateContent('adm_site_section', 'ajax.php', 'action=list_acls');" title="ACLs">
			<div class="menu_button" style="background: #FEFCFF;">
			<img src="<?php echo $config["html_root"] . "/rs/img/key-to-success.png"; ?>" alt="Service Settings"/><p>Acls</p></div></a>

			<a href="javascript:updateContent('adm_site_section', 'ajax.php', 'action=list_groups');" title="Grupos">
			<div class="menu_button" style="background: #FEFCFF;">
			<img src="<?php echo $config["html_root"] . "/rs/img/group-of-businessmen.png"; ?>" alt="Service Settings"/><p>Grupos</p></div></a>
		</nav>
	</section>
	<section id="adm_site_section">
		<div id="hidden_div1">
			<a href="javascript:show('hidden_rol1');" title="Rol: Admin">
			<div class="menu_button" style="background: #FEFCFF;">
			<img src="<?php echo $config["html_root"] . "/rs/img/web.png"; ?>" alt="Service Settings"/><p>Rol: Admin</p></div></a>
			<div id="hidden_rol1">
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
			</div>
			<a href="javascript:show('hidden_rol2');" title="Rol: Manager">
			<div class="menu_button" style="background: #FEFCFF;">
			<img src="<?php echo $config["html_root"] . "/rs/img/computer.png"; ?>" alt="Service Settings"/><p>Rol: Manager</p></div></a>
			<div id="hidden_rol2">	
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
			</div>
			<a href="javascript:show('hidden_rol3');" title="Rol: Usuario">
			<div class="menu_button" style="background: #FEFCFF;">
			<img src="<?php echo $config["html_root"] . "/rs/img/call.png"; ?>" alt="Service Settings"/><p>Rol: Usuario</p></div></a>
			<div id="hidden_rol3">	
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
			</div>
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

		<div id="content_group">
			<p>Cargando...</p>
		</div>
	</section>
		<!--			
		<a href="#">M&aacute;s cosas</a>
		-->
	<div class="button_return">	
		<a href="<?php echo $config["html_root"] . "/?m=adm" ?>" title="Return">
		<img src="<?php echo $config["html_root"] . "/rs/img/web-7.png"; ?>" alt="Service Settings"/></a>
	</div>
</body>

</html>