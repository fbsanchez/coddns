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

$auth_level_required = get_required_auth_level('ajax','','');
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

    $limit = ITEMS_PER_PAGE;

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

	if ($page < 1) {
		$page = 1;
	}
	if ($limit <= 0){
		$limit = ITEMS_PER_PAGE;
	}
	$offset = ($page-1) * $limit;

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


	$dbclient->connect() or die ($dbclient->lq_error());

	$q = "select h.tag, coalesce((select hh.tag from hosts hh where h.rid=hh.id),h.ip) value, r.tag record, h.ttl from hosts h, record_types r, users u where h.rtype=r.id and h.oid=u.id and u.mail='" . $_SESSION["email"] . "' ORDER BY $sort_index LIMIT $limit OFFSET $offset";

	$r = $dbclient->exeq($q) or die ($dbclient->lq_error());

	$nrows = $dbclient->lq_nresults();


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

	    <script type="text/javascript">
	    	item_count=<?php echo $nrows; ?>;
	    	nrows.innerHTML=" (" + item_count + ")";
	    </script>
	<?php
	}
}



$action    = secure_get("action");
$arguments = secure_get("args","json");

switch ($action) {
	case 'list_hosts':
		list_hosts($arguments);
		break;
	default:
		print "Unknown action";
		break;
}


?>