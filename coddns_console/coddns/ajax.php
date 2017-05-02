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

require_once (__DIR__ . "/include/config.php");
require_once (__DIR__ . "/lib/db.php");
require_once (__DIR__ . "/lib/htmlwriter.php");
require_once (__DIR__ . "/include/functions_util.php");
require_once (__DIR__ . "/lib/coduser.php");

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

	$auth_level_required = get_required_auth_level('usr','hosts','');
	$user = new CODUser();
	$user->check_auth_level($auth_level_required);

    $dbclient = $config["dbh"];

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
		$ip_filter   = $dbclient->prepare($data[4], "ip_addr");
	}

	if ($page < 0) {
		$page = 0;
	}
	if ($limit <= 0){
		$limit = ITEMS_PER_PAGE;
	}
	$offset = $page * $limit;

	switch ($sortby){
		case "group": $sort_index = 1;break;
		case "tag":   $sort_index = 2;break;
		case "value": $sort_index = 3;break;
		case "rr":    $sort_index = 4;break;
		case "ttl":   $sort_index = 5;break;
		case "owner": $sort_index = 6;break;
		default: $sort_index=2;
	}

	if ("$sort_m" == "invert"){
		$sort_index .= " desc ";
	}

	// Get total host counter - unlimited
	$q  = "select g.tag as \"group\", h.tag, coalesce((select hh.tag from hosts hh where h.rid=hh.id),h.ip) as value, r.tag as record, h.ttl, (select mail from users where id=h.oid) as mail "
		. " from hosts h, record_types r, users u, groups g, tusers_groups ug, zones z "
		. " where z.id=h.zone_id and h.rtype=r.id and h.gid=ug.gid "
		. "  and ((z.is_public=0 and ug.admin=1 and u.id=ug.oid and ug.gid=g.id) "
		. "   or (z.is_public=0 and (ug.view=1 or ug.edit=1) and u.id=ug.oid and ug.gid=g.id and u.id=h.oid) "
		. "   or (z.is_public=1 and (ug.view=1 or ug.edit=1 or ug.admin=1) and u.id=ug.oid and ug.gid=g.id and u.id=h.oid) "
		. "   or (z.is_public=1 and ug.admin=1 and u.id=ug.oid and ug.gid=g.id)) "
		. "  and u.mail='" . $_SESSION["email"] . "' ";

	if (isset($text_filter) && ($text_filter != "")){
		$q .= " and (lower(h.tag) like lower('%" . $text_filter . "%') ";
		if (isset($ip_filter) && $ip_filter > 0){
			$q .= " OR INET_NTOA(h.ip) like \"%" . $ip_filter . "%\") ";
		}
		else {
			$q .= ") \n";
		}
	}

	$r = $dbclient->exeq($q) or die ($dbclient->lq_error());
	$nrows = $dbclient->lq_nresults();

	$q  = "select g.tag as \"group\", h.tag, coalesce((select hh.tag from hosts hh where h.rid=hh.id),h.ip) as value, r.tag as record, h.ttl, (select mail from users where id=h.oid) as mail "
		. " from hosts h, record_types r, users u, groups g, tusers_groups ug, zones z "
		. " where z.id=h.zone_id and h.rtype=r.id and h.gid=ug.gid "
		. "  and ((z.is_public=0 and ug.admin=1 and u.id=ug.oid and ug.gid=g.id) "
		. "   or (z.is_public=0 and (ug.view=1 or ug.edit=1) and u.id=ug.oid and ug.gid=g.id and u.id=h.oid) "
		. "   or (z.is_public=1 and (ug.view=1 or ug.edit=1 or ug.admin=1) and u.id=ug.oid and ug.gid=g.id and u.id=h.oid) "
		. "   or (z.is_public=1 and ug.admin=1 and u.id=ug.oid and ug.gid=g.id)) "
		. "  and u.mail='" . $_SESSION["email"] . "' ";

	if (isset($text_filter) && ($text_filter != "")){
		$q .= " and (lower(h.tag) like lower('%" . $text_filter . "%') ";
		if (isset($ip_filter) && $ip_filter > 0){
			$q .= " OR INET_NTOA(h.ip) like \"%" . $ip_filter . "%\") ";
		}
		else {
			$q .= ") \n";
		}
	}
	$q .= " ORDER BY $sort_index LIMIT $limit OFFSET $offset";
	
	$r = $dbclient->exeq($q) or die ($dbclient->lq_error());

	$del_submit= "fsgo('del', 'ajax_message','usr/hosts/hosts_rq_del.php', true,raise_ajax_message);return false;";
	while ($row = $dbclient->fetch_array ($r)) {
	?>
	    <tr>
	        <td><?php echo $row["tag"];?></td>
	        <td><?php echo $row["group"];?></td>
	        <td><?php echo $row["record"];?></td>
	        <td><?php
	            if($row["record"] == "A"){
	                echo long2ip($row["value"]);
	            }
	            else {
	                echo $row["value"];
	            }
	            ?></td>
	        <?php
	        	// if user is administrator, show the owner of the "private" hosts
                if($user->is_global_admin()){
                    echo "<td>" . $row["mail"] . "</td>";
                }
	        ?>
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

    $dbclient = $config["dbh"];

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

    $dbclient = $config["dbh"];

	$q = "select * from groups;";
	$r = $dbclient->exeq($q) or die ($dbclient->lq_error());

	$q = "select tag from groups;";
	$s = $dbclient->exeq($q) or die ($dbclient->lq_error());
	?>

	<div>
		<button class="button_users" onclick="toggleDisplay('form_create_group')">Crear grupo</button>
		<button class="button_users" onclick="delete_all_groups();toggleDisplay('form_delete_all_group'); ">Eliminar grupos</button>
	</div>

	<table>
		<thead>
			<tr>
				<td>Nombre</td>
				<td>Descripci&oacuten</td>
				<td>Miembros</td>
				<td>Grupo Padre</td>
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
		        	<?php
		        		$m = "select tag from groups where id = '" . $row["parent"] . "';";
						$t = $dbclient->exeq($m) or die ($dbclient->lq_error());
						$u = $dbclient->exeq($m) or die ($dbclient->lq_error());
						if($row2 = $dbclient->fetch_array ($u)){
							while ($row2 = $dbclient->fetch_array ($t)){
		        				echo $row2["tag"];
		        			}
		        		} else {
		        			echo "none";
		        		}
		        	?>
		        </td>
		        <td>
		        	<ul class= "actions_form">
		        		<li>
		        			<a href="#"><img src="<?php echo $config["html_root"] . "/rs/img/edit.png";?>" title="Editar" /></a>
		        		</li>
		        		<li>
		        			<a href="#" onclick="toggleDisplay('form_delete_group'); add_name_to_delete('<?php echo $row["tag"];?>');">
		        				<img src="<?php echo $config["html_root"] . "/rs/img/delete.png";?>" title="Eliminar"/>
		        			</a>
		        		</li>
		        		<li>
		        			<input id ="delete_<?php echo $row["tag"];?>" class="delete_checkbox" type="checkbox"/>
		        		</li>
		        	</ul>
		        </td>
		    </tr>
	    </tbody>
	<?php
	}
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

    $dbclient = $config["dbh"];

	$q = "select sa.tag, r.tag as rol from roles r, site_acl sa where sa.auth_level=r.auth_level;";
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
}




function adm_server_control_checkconf($data) {
	global $config;

	$auth_level_required = get_required_auth_level('adm','server','control');
	$user = new CODUser();
	$user->check_auth_level($auth_level_required);

	require_once(__DIR__ . "/include/functions_server.php");
	
	$servername = secure_get("id");
	$sshclient = get_server_connection($servername);

	if ($sshclient !== false) {

		// Check if we're connected & authenticated into the server
		if (! $sshclient->is_authenticated()){
			echo "Datos de acceso no v&aacute;lidos";
			return false;
		}

		$server = $sshclient->get_server_info();

		$result = $sshclient->launch ("named-checkconf " . $server->main_config_file);

		if (strlen($result[0] == "") && $result[1] == "") {
			echo "<img src='" . $config["html_root"] . "/rs/img/status_ok.png' style='width: 10px; margin: 0 15px;'/>";
			echo "Bind configuration file " . $server->main_config_file . " is valid.";
		}
		else {
			echo "<img src='" . $config["html_root"] . "/rs/img/status_err.png' style='width: 10px; margin: 0 15px;'/>";
			echo "Bind configuration file " . $server->main_config_file . " <b>is not</b> es valid. Exceptions summary:<br>";
			echo "<pre>";
			echo $result[0];
			echo $result[1];
			echo "</pre>";

		}

		$sshclient->disconnect();

		return true;
	}
	
	return false;
}


function adm_server_control_restart($data) {
	global $config;

	require_once(__DIR__ . "/include/functions_server.php");
	
	$servername = secure_get("id");
	$sshclient = get_server_connection($servername);

	if ($sshclient !== false) {

		// Check if we're connected & authenticated into the server
		if (! $sshclient->is_authenticated()){
			echo "Datos de acceso no v&aacute;lidos";
			return false;
		}

		$result = $sshclient->launch ("systemctl restart named || /etc/init.d/named restart || service named restart");
		echo "<img src='" . $config["html_root"] . "/rs/img/status_ok.png' style='width: 10px; margin: 0 15px;'/>";
		echo "<pre>";
		echo $result[0];
		echo "\n";
		echo "</pre>";

		$sshclient->disconnect();

		return true;
	}
	
	return false;
}


function adm_server_control_clear_cache($data) {
	global $config;

	require_once(__DIR__ . "/include/functions_server.php");
	
	$servername = secure_get("id");
	$sshclient = get_server_connection($servername);

	if ($sshclient !== false) {
		// Check if we're connected & authenticated into the server
		if (! $sshclient->is_authenticated()){
			echo "Datos de acceso no v&aacute;lidos";
			return false;
		}

		$result = $sshclient->launch ("rndc flush");
		echo "<img src='" . $config["html_root"] . "/rs/img/status_ok.png' style='width: 10px; margin: 0 15px;'/>";
		if ($result[0] == "") {
			echo "Cache cleared.";
		}
		else {
			echo "<pre>";
			echo $result[0];
			echo $result[1];
			echo "\n";
			echo "</pre>";
		}
		
		$sshclient->disconnect();

		return true;
	}
	
	return false;

}


function list_servers() {
	global $config;

	$auth_level_required = get_required_auth_level('adm','servers','');
	$user = new CODUser();
	$user->check_auth_level($auth_level_required);

	require_once(__DIR__ . "/include/functions_server.php");
	
	$dbclient = $config["dbh"];

	$q = "select s.*,(select count(*) from zones z, zone_server sz where sz.id_server=s.id and sz.id_zone=z.id ) as nzones from servers s;";
	$results = $dbclient->exeq($q) or die ($dbclient->lq_error());

	while ($r = $dbclient->fetch_object($results)) {

			$named_ok = -1;

			$server_conn = get_server_connection_from_hash($r);
			
			if ($server_conn !== false) {
				// check named service:
			
				$out = $server_conn->launch ("rndc status | grep running | wc -l");
				
				if (($out[0] >= 1) && ($out[1] == "")) {
					$named_ok  = 1;
				}
				elseif (($out[0] == 0) && ($out[1] == "")) {
					$named_ok = 0;
				}
				else {
					$err_msg = $out[1];
				}
			}

		?>

		<div class="server">

			<a id="show_<?php echo $r->tag;?>" href="<?php echo $config["html_root"];?>?m=adm&z=server&op=manager&id=<?php echo $r->tag; ?>#status">
			<?php 
			echo "<img src=\"";

			if ($named_ok == 1) {
				echo $config["html_root"] . "/rs/img/server_up_50.png";
				$status = "Online";
			}
			elseif($named_ok == 0) {
				echo $config["html_root"] . "/rs/img/server_down_50.png";
				$status = "Offline";
			}
			else {
				echo $config["html_root"] . "/rs/img/server_warning_50.png";
				$status = "Unknown";
			}
			echo "\" alt='server status'/>";
			?>
			</a>
			<div class="server_summary">
			<p>Server: <?php echo $r->tag;?></p>
			<p>IP/FQDN: <?php echo $r->ip;?></p>
			<p>Status: <?php echo $status;?></p>
			<p>Zones loaded: <?php echo $r->nzones;?></p>
			</div>
		</div>
	<?php
	}
}










//
// AJAX API Control
//

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
	case 'list_servers':
		list_servers($arguments);
		break;
	case 'exist_tag_groups':
		exist_tag_groups($arguments);
		break;
	case 'checkconf':
		adm_server_control_checkconf($arguments);
		break;
	case 'restart_service':
		adm_server_control_restart($arguments);
		break;
	case 'clear_cache':
		adm_server_control_clear_cache($arguments);
		break;
	default:
		print "Unknown action";
		break;
}



?>
