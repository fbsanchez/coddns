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
	 function linkselected(id_elem){
	 	var id = document.getElementById(id_elem);
	 	
	 	//diferents id links
	 	var link_users  = document.getElementById('link_users');
	 	var link_roles  = document.getElementById('link_roles');
	 	var link_groups = document.getElementById('link_groups');
	 	var link_acls   = document.getElementById('link_acls');

	 	//remove class selected the all links
	 	link_users.className  = link_users.className.replace('selected', '');
	 	link_roles.className  = link_roles.className.replace('selected', '');
	 	link_groups.className = link_groups.className.replace('selected', '');
	 	link_acls.className   = link_acls.className.replace('selected', '');

	 	//revert img original
	 	link_users.childNodes[1].childNodes[1].setAttribute("src", "<?php echo $config["html_root"] . "/rs/img/teamwork-in-the-office.png"; ?>");
	 	link_roles.childNodes[1].childNodes[1].setAttribute("src", "<?php echo $config["html_root"] . "/rs/img/group-of-businessmen.png"; ?>");
	 	link_groups.childNodes[1].childNodes[1].setAttribute("src", "<?php echo $config["html_root"] . "/rs/img/web-5.png"; ?>");
	 	link_acls.childNodes[1].childNodes[1].setAttribute("src", "<?php echo $config["html_root"] . "/rs/img/key-to-success.png"; ?>");

	 	//add class selected on click link
	 	id.className = 'selected';

	 	//change img onclick link
	 	if (id_elem == 'link_users'){
	 		id.childNodes[1].childNodes[1].setAttribute("src", "<?php echo $config["html_root"] . "/rs/img/business.png"; ?>");
	 	} else if (id_elem == 'link_roles'){
	 		id.childNodes[1].childNodes[1].setAttribute("src", "<?php echo $config["html_root"] . "/rs/img/people-1.png"; ?>");
	 	} else if (id_elem == 'link_groups'){
	 		id.childNodes[1].childNodes[1].setAttribute("src", "<?php echo $config["html_root"] . "/rs/img/web-white-5.png"; ?>");
	 	} else {
	 		id.childNodes[1].childNodes[1].setAttribute("src", "<?php echo $config["html_root"] . "/rs/img/people-2.png"; ?>");
	 	}
	 }
</script>
<body>
	<section>
		<h2>Panel de administraci&oacute;n del sitio</h2>
		<nav
>			<ul id="tabs">
				<li><a href="javascript:updateContent('adm_site_section', 'ajax.php', 'action=list_users');" onclick="linkselected('link_users')" title="Usuarios" id="link_users">
				<div class="menu_button">
				<img src="<?php echo $config["html_root"] . "/rs/img/teamwork-in-the-office.png"; ?>" alt="Service Settings"/><p>Usuarios</p></div></a></li>
				<li><a href="javascript:updateContent('adm_site_section', 'ajax.php', 'action=list_groups');" onclick="linkselected('link_groups')" title="Grupos" id="link_groups">
				<div class="menu_button">
				<img src="<?php echo $config["html_root"] . "/rs/img/web-5.png"; ?>" alt="Service Settings"/><p>Grupos</p></div></a></li>
				<li><a href="javascript:updateContent('adm_site_section', 'ajax.php', 'action=list_roles');" onclick="linkselected('link_roles')" title="Roles" id="link_roles">
				<div class="menu_button">
				<img src="<?php echo $config["html_root"] . "/rs/img/group-of-businessmen.png"; ?>" alt="Roles"/><p>Roles</p></div></a></li>
				<li><a href="javascript:updateContent('adm_site_section', 'ajax.php', 'action=list_acls');" onclick="linkselected('link_acls')" title="ACLs" id="link_acls">
				<div class="menu_button">
				<img src="<?php echo $config["html_root"] . "/rs/img/key-to-success.png"; ?>" alt="Service Settings"/><p>Acls</p></div></a></li>
			</ul>
		</nav>
	</section>
	<section id="adm_site_section">
		<div id="content_group">

		</div>
	</section>
	<a href="<?php echo $config["html_root"] . "/?m=adm" ?>" title="Return" class="button_return">
	<img src="<?php echo $config["html_root"] . "/rs/img/web-7.png"; ?>" alt="Service Settings"/></a>
</body>

</html>