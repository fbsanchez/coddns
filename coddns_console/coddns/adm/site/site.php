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
require_once(__DIR__ . "/../../lib/util.php");
require_once(__DIR__ . "/../../lib/coduser.php");

if (! defined("_VALID_ACCESS")) { // Avoid direct access
    header ("Location: " . $config["html_root"] . "/");
    exit (1);
}

$auth_level_required = get_required_auth_level('adm','site','');
$user = new CODUser();
$user->check_auth_level($auth_level_required);

$dbclient = new DBClient($config["db_config"]) or die ($dbclient->lq_error());

$dbclient->connect();

$q = "select tag from roles;";
$r = $dbclient->exeq($q) or die ($dbclient->lq_error());

$l = "select tag from groups;";
$s = $dbclient->exeq($l) or die ($dbclient->lq_error());

$m = "select * from groups;";
$t = $dbclient->exeq($m) or die ($dbclient->lq_error());
?>


<!DOCTYPE HTML>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="rs/css/pc/adm_site.css">
	<link rel="stylesheet" type="text/css" href="rs/css/pc/pop_up.css">
</head>
<script languague="javascript">
	var anchors = location.href.split('#');
	window.onload = function (){
		var tab= anchors[1];
		if (document.getElementById(tab)){
			document.getElementById(tab).onclick();
		}
	}
	
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
		
	//pass from multiselect other multiselect items ordered
	function SelectMoveRows(SS1,SS2) {
	    var SelID='';
	    var SelText='';
	    for (i=SS1.options.length - 1; i>=0; i--) {
	        if (SS1.options[i].selected == true) {
	            SelID=SS1.options[i].value;
	            SelText=SS1.options[i].text;
	            var newRow = new Option(SelText,SelID);
	            SS2.options[SS2.length]=newRow;
	            SS1.options[i]=null;
	        }
	    }
	}
</script>
<body onload="javascript:updateContent('adm_site_section', 'ajax.php', 'action=list_users');">
	<section>
		<h2>Panel de administraci&oacute;n del sitio</h2>
		<nav
>			<ul id="tabs">
				<li><a href="#link_users" onclick="linkselected('link_users'); updateContent('adm_site_section', 'ajax.php', 'action=list_users');" title="Usuarios" id="link_users">
				<div class="menu_button">
				<img src="<?php echo $config["html_root"] . "/rs/img/teamwork-in-the-office.png"; ?>" alt="Service Settings"/><p>Usuarios</p></div></a></li>
				<li><a href="#link_groups" onclick="linkselected('link_groups'); updateContent('adm_site_section', 'ajax.php', 'action=list_groups');" title="Grupos" id="link_groups">
				<div class="menu_button">
				<img src="<?php echo $config["html_root"] . "/rs/img/web-5.png"; ?>" alt="Service Settings"/><p>Grupos</p></div></a></li>
				<li><a href="#link_roles" onclick="linkselected('link_roles'); updateContent('adm_site_section', 'ajax.php', 'action=list_roles');" title="Roles" id="link_roles">
				<div class="menu_button">
				<img src="<?php echo $config["html_root"] . "/rs/img/group-of-businessmen.png"; ?>" alt="Roles"/><p>Roles</p></div></a></li>
				<li><a href="#link_acls" onclick="linkselected('link_acls'); updateContent('adm_site_section', 'ajax.php', 'action=list_acls');" title="ACLs" id="link_acls">
				<div class="menu_button">
				<img src="<?php echo $config["html_root"] . "/rs/img/key-to-success.png"; ?>" alt="Service Settings"/><p>Acls</p></div></a></li>
			</ul>
		</nav>
	</section>

	<div id="form_create_users" class="pop_up_form" style="display:none;" draggable>
		<form name="new_users" id="newusers" method="POST" action="" onsubmit="fsgo('newusers', 'ajax_message','adm/site/site_rq_new_user.php', true, raise_ajax_message);return false;">
			<div class="pop_up_headers">
				<h3>Crear Usuarios</h3>
				<button class="pop_up_headers_close" type="button" onclick="toggleDisplay('form_create_users')">X</button>
			</div>
			<div class="pop_up_body">
				<ul>
					<li>
						<label for="user_email">Email:</label>
						<input type="email" id="user_email" name="email" placeholder="user@coddns.org" required="required"></input>
					</li>
					<li>
						<label for="user_rol">Rol:</label>
						<select id="user_rol" name="rol">
							<?php
							while ($row = $dbclient->fetch_array ($r)) {
							?>
								<option><?php echo $row['tag']; ?></option>
							<?php
							}
							?>
						</select>
					</li>
					<li>
						<label for="user_groups">Grupos:</label>
					</li>
					<li>
						<select id="user_groups" name="select_groups" multiple>
							<?php
							while ($row = $dbclient->fetch_array ($s)) {
							?>
								<option><?php echo $row['tag']; ?></option>
							<?php
							}
							?>
						</select>
						<div class="multiselect_button">
							<button onClick="SelectMoveRows(document.new_users.select_groups,document.new_users.groups)">></button>
							<button onClick="SelectMoveRows(document.new_users.groups, document.new_users.select_groups)"><</button>
						</div>
						<select name="groups" multiple>
						</select>
					</li>
				</ul>
			</div>
			<div class="pop_up_footer">
				<input type="submit" name="Enviar"></input>
			</div>
		</form>
	</div>
	
	<div id="form_delete_users" class="pop_up_form" style="display:none;" draggable>
		<form method="POST" action="">
			<div class="pop_up_headers">
				<h3>Borrar Usuarios</h3>
				<button class="pop_up_headers_close" type="button" onclick="toggleDisplay('form_delete_users')">X</button>
			</div>
			<div class="pop_up_body">
				<p>¿Estas seguro de eliminar los usuarios seleccionados?</p>
				<p>¿Deseas eliminar a todos los usuarios menos el admnistrador?</p>
			</div>
			<div class="pop_up_footer">
				<input type="submit" name="Enviar"></input>
			</div>
		</form>
	</div>

	<div id="form_create_group" class="pop_up_form" style="display:none;" draggable>
		<form name="new_group" id="newgroup" method="POST" action="" onsubmit="fsgo('newgroup', 'ajax_message','adm/site/site_rq_new_group.php', true, raise_ajax_message);return false;">
			<div class="pop_up_headers">
				<h3>Crear Grupos</h3>
				<button class="pop_up_headers_close" type="button" onclick="toggleDisplay('form_create_group')">X</button>
			</div>
			<div class="pop_up_body">
				<ul>
					<li>
						<label for="group_name">Nombre</label>
						<input type="text" id="group_name" name="tag" required="required"></input>
					</li>
					<li>
						<label for="group_description">Descripci&oacuten</label>
						<input type="text" id="group_description" name="description"></input>
					</li>
					<li>
						<label for="group_parent">Padre</label>
						<select id="group_parent" name="parent">
							<option value=-1>None</option>
							<?php
							while ($row = $dbclient->fetch_array ($t)) {
							?>
								<option value=<?php echo "'" . $row['id'] . "'"; ?>><?php echo $row['tag']; ?></option>
							<?php
							}
							?>
						</select>
					</li>
					<li>
						<label id ="error_form_create_groups"> </label>
					</li>
				</ul>
			</div>
			<div class="pop_up_footer">
				<input type="submit" name="Enviar"></input>
			</div>
		</form>
	</div>

	<div id="form_delete_group" class="pop_up_form" style="display:none;" draggable>
		<form method="POST" action="">
			<div class="pop_up_headers">
				<h3>Borrar Grupo</h3>
				<button class="pop_up_headers_close" type="button" onclick="toggleDisplay('form_delete_group')">X</button>
			</div>
			<div class="pop_up_body">
				<p>¿Estas seguro de eliminar el grupo seleccionado?</p>
				<input type="hidden" name="delete" value="" id="delete-item">
			</div>
			<div class="pop_up_footer">
				<input type="submit" name="Enviar"></input>
			</div>
		</form>
	</div>

	
	<section id="adm_site_section">
	
	</section>
	<a href="<?php echo $config["html_root"] . "/?m=adm" ?>" title="Return" class="button_return">
	<img src="<?php echo $config["html_root"] . "/rs/img/web-7.png"; ?>" alt="Service Settings"/></a>
</body>

</html>