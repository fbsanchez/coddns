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
 * <date>2016-03-01</date>
 * <update>2016-03-01</udate>
 * <summary> </summary>
 */

require_once (dirname(__FILE__) . "/include/config.php");
require_once (dirname(__FILE__) . "/lib/db.php");
require_once (dirname(__FILE__) . "/lib/util.php");
require_once (dirname(__FILE__) . "/lib/coduser.php");

$auth_level_required = get_required_auth_level('','ajax','');
$user = new CODUser();
$user->check_auth_level($auth_level_required);

/**
 * $sortby
 * $offset = 0
 * $limit = 15
 */
function list_hosts($data){
	global $config;

    $dbclient = new DBClient($config["db_config"]) or die ($dbclient->lq_error());
    $dbclient->connect() or die ($dbclient->lq_error());

    $limit = ITEMS_PER_PAGE;
    $text_filter = null;
    $ip_filter   = 0;

    if (isset ($data[0])) {
		$sortby = $dbclient->prepare($data[0],"letters");
	}
	if (isset ($data[1])) {
		$sort_m = $dbclient->prepare($data[1],"letters");
	}
	if (isset ($data[2])) {
		$page   = $dbclient->prepare($data[2],"number");
	}
	if (isset ($data[3])) {
		$limit  = $dbclient->prepare($data[3],"number");
	}
	if (isset ($data[4])) {
		$text_filter = $dbclient->prepare($data[4],"letters++");
		$ip_filter   = ip2long($text_filter);
	}

	if ($page < 0) {
		$page = 0;
	}
	if ($limit <= 0){
		$limit = ITEMS_PER_PAGE;
	}
	$offset = $page * $limit;

	switch ($sortby){
		case "tag":   $sort_index = 1;break;
		case "value": $sort_index = 2;break;
		case "rr":    $sort_index = 3;break;
		case "ttl":   $sort_index = 4;break;
		default: $sort_index=3;
	}

	if ("$sort_m" == "invert"){
		$sort_index .= " desc ";
	}


	// Get total host counter - unlimited
	$q = "select h.tag, coalesce((select hh.tag from hosts hh where h.rid=hh.id),h.ip) as value, r.tag as record, h.ttl from hosts h, record_types r where h.rtype=r.id and h.gid in (select ug.gid\n from tusers_groups ug, users u \nwhere (ug.view=1 or ug.admin=1) and u.id=ug.oid and u.mail='" . $_SESSION["email"] . "') ";
	if (isset($text_filter) && ($text_filter != "")){
		$q .= " and (lower(h.tag) like lower('%" . $text_filter . "%') ";
		if (isset($ip_filter) && $ip_filter > 0){
			$q .= " OR h.ip = $ip_filter) ";
		}
		else {
			$q .= ") \n";
		}
	}

	$r = $dbclient->exeq($q) or die ($dbclient->lq_error());
	$nrows = $dbclient->lq_nresults();

	$q = "select h.tag, coalesce((select hh.tag from hosts hh where h.rid=hh.id),h.ip) as value, r.tag as record, h.ttl from hosts h, record_types r, users u where h.rtype=r.id and h.oid=u.id and h.gid in (select ug.gid from tusers_groups ug, users u where (ug.view=1 or ug.admin=1) and u.id=ug.oid and u.mail='" . $_SESSION["email"] . "') ";
	if (isset($text_filter) && ($text_filter != "")){
		$q .= " and (lower(h.tag) like lower('%" . $text_filter . "%') ";
		if (isset($ip_filter) && $ip_filter > 0){
			$q .= " OR h.ip = $ip_filter) ";
		}
		else {
			$q .= ") \n";
		}
	}
	$q .= " ORDER BY $sort_index LIMIT $limit OFFSET $offset";


	$r = $dbclient->exeq($q) or die ($dbclient->lq_error());

	$del_submit= "fsgo('del', 'ajax_message','usr/hosts_rq_del.php', true,raise_ajax_message);return false;";
	while ($row = $dbclient->fetch_array ($r)) {
	?>
	    <tr>
	        <td><?php echo $row["tag"];?></td>
	        <td><?php echo $row["record"];?></td>
	        <td><?php
	            if($row["record"] == "A"){
	                echo long2ip($row["value"]);
	            }
	            else {
	                echo $row["value"];
	            }
	            ?></td>
	        <td><?php echo $row["ttl"];?></td>
	        <td class='edit' style="url('<?php echo $config["html_root"];?>/rs/img/delete.png')" title='editar' onclick="editip.value='<?php echo $row["value"]; ?>';edith.value='<?php echo $row["tag"]; ?>';change.submit();"></td>
	        <td class='del' title='eliminar' onclick="delh.value='<?php echo $row["tag"];?>'; if (confirm('Seguro que desea eliminar <?php echo $row["tag"];?>?')) {<?php echo $del_submit;?>}"></td>
	    </tr>
	    <?php
		}
		?>
	    <script type="text/javascript">
	    	item_count=<?php echo $nrows; ?>;
	    	nrows.innerHTML=" (" + item_count + ")";
	    </script>
	<?php

	$dbclient->disconnect();
}

/**
 * Function to list all users available for the current $_SESSION["user"]
 *
 */
function list_users($data) {
	global $config;
	// Minimal access to manage site ACL
	$auth_level_required = get_required_auth_level('adm','site','');
	$user = new CODUser();
	$user->check_auth_level($auth_level_required);

    $dbclient = new DBClient($config["db_config"]) or die ($dbclient->lq_error());

    $dbclient->connect();

	$q = "select u.mail as mail, r.tag as rol, u.ip_last_login as ip_last_login, u.last_login as last_login, u.first_login as first_login from users u, roles r where r.id=u.rol;";
	$r = $dbclient->exeq($q) or die ($dbclient->lq_error());

	?>
	<div>
		<button class="button_users" onclick="toggleDisplay('form_create_users')">Crear usuario</button>
		<button class="button_users" onclick="toggleDisplay('form_delete_users')">Eliminar usuarios</button>
	</div>

	<table>
		<thead>
			<tr>
				<td>Email</td>
				<td>Rol</td>
				<td>&Uacuteltimo acceso</td>
				<td>Ip &Uacuteltimo acceso</td>
				<td>Miembro desde</td>
				<td>Acciones</td>
				<td><input type="checkbox" /></td>
			</tr>
		</thead>
	<?php
	while ($row = $dbclient->fetch_array ($r)) {
	?>
		<tbody>
		    <tr>
		        <td><?php echo $row["mail"];?></td>
		        <td><?php echo $row["rol"];?></td>
		        <td><?php echo $row["ip_last_login"]?></td>
		        <td><?php echo $row["last_login"];?></td>
		        <td><?php echo $row["first_login"];?></td>
		        <td>
		        	<a href="#"><img src="<?php echo $config["html_root"] . "/rs/img/edit.png";?>" title="Editar" /></a>
		        	<a href="#"><img src="<?php echo $config["html_root"] . "/rs/img/delete.png";?>" title="Eliminar"/></a>
		        </td>
		        <td><input type="checkbox" /></td>
		    </tr>
	    </tbody>
	<?php
	}

	$dbclient->disconnect();
}

/**
 * Function to list all groups available for the current $_SESSION["user"]
 *
 */
function list_groups($data) {
	global $config;
	// Minimal access to manage site ACL
	$auth_level_required = get_required_auth_level('adm','site','');
	$user = new CODUser();
	$user->check_auth_level($auth_level_required);

    $dbclient = new DBClient($config["db_config"]) or die ($dbclient->lq_error());

    $dbclient->connect();

	$q = "select * from groups;";
	$r = $dbclient->exeq($q) or die ($dbclient->lq_error());

	?>

	<div>
		<button class="button_users">Crear grupo</button>
		<button class="button_users">Eliminar grupos</button>
	</div>

	<table>
		<thead>
			<tr>
				<td>Nombre</td>
				<td>Descripci&oacuten</td>
				<td>Miembros</td>
				<td>Acciones</td>
			</tr>
		</thead>
	<?php
	while ($row = $dbclient->fetch_array ($r)) {
	?>
		<tbody>
		    <tr>
		        <td><?php echo $row["tag"];?></td>
		        <td><?php echo $row["description"];?></td>
		        <td>
		        	<?php
			        	$l = "select count(u.id) as count_user from users u, groups g, tusers_groups ug where u.id = ug.oid and g.id = ug.gid and g.tag = '" . $row["tag"] . "';";
						$s = $dbclient->exeq($l) or die ($dbclient->lq_error());
			        	while ($row2 = $dbclient->fetch_array ($s)){
			        		if ($row2["count_user"]){
			        			echo $row2["count_user"];
			        		}
			        		else {
			        			echo 0;
			        		}
			        	}
		        	?>
		        </td>
		        <td>
		        	<a href="#"><img src="<?php echo $config["html_root"] . "/rs/img/edit.png";?>" title="Editar" /></a>
		        	<a href="#"><img src="<?php echo $config["html_root"] . "/rs/img/delete.png";?>" title="Eliminar"/></a>
		        	<input type="checkbox"/>
		        </td>
		    </tr>
	    </tbody>
	<?php
	}
	$dbclient->disconnect();
}


/**
 * Function to list all ACLs available for the current $_SESSION["user"]
 *
 */
function list_acls($data) {
	global $config;
	// Minimal access to manage site ACL
	$auth_level_required = get_required_auth_level('adm','site','');
	$user = new CODUser();
	$user->check_auth_level($auth_level_required);

    $dbclient = new DBClient($config["db_config"]) or die ($dbclient->lq_error());

    $dbclient->connect();

	$q = "select sa.tag, r.tag as rol from roles r, site_acl sa where sa.auth_level=r.auth_level;
";
	$r = $dbclient->exeq($q) or die ($dbclient->lq_error());

	$l = "select tag from roles;";
	$s = $dbclient->exeq($l) or die ($dbclient->lq_error());

	?>

	<table>
		<thead>
			<tr>
				<td>P&aacutegina</td>
				<td>Nivel de Aturizaci&oacuten	</td>
			</tr>
		</thead>
	<?php
	while ($row = $dbclient->fetch_array ($r)) {
	?>
		<tbody>
		    <tr>
		        <td><?php echo $row["tag"];?></td>
		        <td>
		        	<select>
		        		<?php while ($row2 = $dbclient->fetch_array ($s)) { ?>
		        		<option selected="<?php echo $row2["auth_level"];?>"><?php echo $row2['tag']; ?></option>
		        		<?php } ?>
		        	</select>
		        </td>
		    </tr>
	    </tbody>
	<?php
	}

	$dbclient->disconnect();
}

$action    = secure_get("action");
$arguments = secure_get("args","json");

switch ($action) {
	case 'list_hosts':
		list_hosts($arguments);
		break;
	case 'list_users':
		list_users($arguments);
		break;
	case 'list_groups':
		list_groups($arguments);
		break;
	case 'list_acls':
		list_acls($arguments);
		break;	
	default:
		print "Unknown action";
		break;
}


?>