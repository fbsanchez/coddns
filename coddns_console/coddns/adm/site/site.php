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
	<link rel="stylesheet" type="text/css" href="<?php echo $config["html_root"];?>/rs/css/pc/tabs.css">
	<link rel="stylesheet" type="text/css" href="rs/css/pc/adm_site.css">
	<link rel="stylesheet" type="text/css" href="rs/css/pc/pop_up.css">
</head>
<script languague="javascript">
	var anchors = location.href.split('#');
	window.onload = function (){
		var tab="link_" + anchors[1];
		if (document.getElementById(tab)){
			document.getElementById(tab).onclick();
		}
	}
	function mark(id){
		document.getElementById("link_users").className="";
		document.getElementById("link_groups").className="";
		document.getElementById("link_roles").className="";
		document.getElementById("link_acls").className="";
		//document.getElementById("adm_content").innerHTML = "Cargando...";
		id.className = "selected";
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

	function add_name_to_delete(id){
		document.getElementById("hidden_name_group_delete").value = id;
	}
</script>

<?php
$clickusers  = "onclick=\"mark(this);updateContent('adm_site_section', 'ajax.php', 'action=list_users');\"";
$clickgroups = "onclick=\"mark(this);updateContent('adm_site_section', 'ajax.php', 'action=list_groups');\"";
$clickroles  = "onclick=\"mark(this);updateContent('adm_site_section', 'ajax.php', 'action=list_roles');\"";
$clickacls   = "onclick=\"mark(this);updateContent('adm_site_section', 'ajax.php', 'action=list_acls');\"";
?>

<body>
	<section>
		<h2>Panel de administraci&oacute;n del sitio</h2>
		<nav>
			<a id="link_users" href="#users" class="" <?php echo $clickusers; ?> >
				Users
			</a>

			<a id="link_groups" href="#groups" class="" <?php echo $clickgroups; ?> >
				Groups
			</a>

			<a id="link_roles" href="#roles" class="" <?php echo $clickroles; ?> >
				Roles
			</a>

			<a id="link_acls" href="#acls" class="" <?php echo $clickacls; ?> >
				ACLS
			</a>
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
		<form name="delete_group" id="delete_group" method="POST" action="" onsubmit="fsgo('delete_group', 'ajax_message','adm/site/site_rq_delete_group.php', true, raise_ajax_message);return false;">
			<div class="pop_up_headers">
				<h3>Borrar Grupo</h3>
				<button class="pop_up_headers_close" type="button" onclick="toggleDisplay('form_delete_group')">X</button>
			</div>
			<div class="pop_up_body">
				<p>¿Estas seguro de eliminar el grupo seleccionado?</p>
				<input type="hidden" id="hidden_name_group_delete" name="tag" required/>
			</div>
			<div class="pop_up_footer">
				<input type="submit" name="Enviar"></input>
			</div>
		</form>
	</div>

	
		<div id="adm_site_section" class="content">
			
		</div>
	
	<a href="<?php echo $config["html_root"] . "/?m=adm" ?>" title="Return" class="button_return">
	<img src="<?php echo $config["html_root"] . "/rs/img/web-7.png"; ?>" alt="Service Settings"/></a>
</body>

</html>