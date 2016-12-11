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

require_once (__DIR__ . "/../../include/config.php");
require_once (__DIR__ . "/../../lib/db.php");
require_once (__DIR__ . "/../../lib/util.php");
require_once (__DIR__ . "/../../lib/coduser.php");

$auth_level_required = get_required_auth_level('usr','hosts','rq_del');
$user = new CODUser();
$user->check_auth_level($auth_level_required);

session_start();
if (!isset($_SESSION["lan"])){
	$_SESSION["lan"] = "es";
}
$lan = $_SESSION["lan"];
session_write_close();

if (! isset ($_POST["delh"]) ){
	die ("Missing parameter, please warn administrator...");
}

$dbclient = new DBClient($db_config);

$dbclient->connect() or die("ERR");

$phost = $dbclient->prepare($_POST["delh"], "url_get");
$fields = explode(".", $phost,2);
$host   = $fields[0];
$domain = $fields[1];

$host = $dbclient->prepare($host, "letters") . "." . $domain;

if($user->is_global_admin()) {
	$q = "delete from hosts where lower(tag)=lower('" . $host . "');";
}
else {
	$q = "delete from hosts where ((oid=(select id from users where lower(mail)=lower('" . $_SESSION["email"] . "')) and gid=(select id from groups where tag='private')) or (gid in (select g.id from groups g, tusers_groups ug, users u where u.id=ug.oid and g.id=ug.gid and (ug.admin=1) and lower(u.mail)=lower('" . $_SESSION["email"] . "')))) and lower(tag)=lower('" . $host . "');";
}

$r = $dbclient->exeq($q);

if ($dbclient->lq_nresults() > 0) {
	// LAUNCH DNS UPDATER
	$out = shell_exec("dnsmgr d " . $host . " A");

	$dbclient->disconnect();

	?>

	<?php
	if (! strlen($out) > 0) {
	?>
	<div>
		<p>Error eliminando <?php echo $host;?>: $out<p>
	</div>

	<?php
	}
	else {
	?>
	<div>
		<p>Se ha eliminado <?php echo $host;?> correctamente<p>
	</div>
	<script type="text/javascript">location.reload();</script>
	<?php
	}
}
else {
	?>
	<div>
		<p>No tiene permiso para eliminar <?php echo $host;?><p>
	</div>
	<?php
}
?>
</body>
</html>
