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
	 
</script>
<body>
	<section>
		<h2>Panel de administraci&oacute;n del sitio</h2>
		<nav>
			<ul id="tabs">
				<li><a href="#users" onclick="javascript:updateContent('adm_site_section', 'ajax.php', 'action=list_users');" title="Usuarios">
				<div class="menu_button">
				<img src="<?php echo $config["html_root"] . "/rs/img/teamwork-in-the-office.png"; ?>" alt="Service Settings"/><p>Usuarios</p></div></a></li>
				<li><a href="#groups" onclick="javascript:updateContent('adm_site_section', 'ajax.php', 'action=list_groups');" title="Grupos">
				<div class="menu_button">
				<img src="<?php echo $config["html_root"] . "/rs/img/web-5.png"; ?>" alt="Service Settings"/><p>Grupos</p></div></a></li>
				<li><a href="#roles" onclick="javascript:updateContent('adm_site_section', 'ajax.php', 'action=list_roles');" title="Roles">
				<div class="menu_button">
				<img src="<?php echo $config["html_root"] . "/rs/img/group-of-businessmen.png"; ?>" alt="Roles"/><p>Roles</p></div></a></li>
				<li><a href="#acls" onclick="javascript:updateContent('adm_site_section', 'ajax.php', 'action=list_acls');" title="ACLs">
				<div class="menu_button">
				<img src="<?php echo $config["html_root"] . "/rs/img/key-to-success.png"; ?>" alt="Service Settings"/><p>Acls</p></div></a></li>
			</ul>
		</nav>
	</section>
	<section id="adm_site_section">
		<div id="content_group">

		</div>
	</section>
	<div class="button_return">	
		<a href="<?php echo $config["html_root"] . "/?m=adm" ?>" title="Return">
		<img src="<?php echo $config["html_root"] . "/rs/img/web-7.png"; ?>" alt="Service Settings"/></a>
	</div>
</body>

</html>