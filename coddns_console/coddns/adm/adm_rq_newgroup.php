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

require_once (__DIR__ . "/../include/config.php");
require_once (__DIR__ . "/../lib/db.php");
require_once (__DIR__ . "/../lib/ipv4.php");
require_once (__DIR__ . "/../lib/util.php");
require_once (__DIR__ . "/../lib/coduser.php");

$auth_level_required = get_required_auth_level('adm','site','rq_new_group');
$user = new CODUser();
$user->check_auth_level($auth_level_required);

echo "<pre>";
var_dump($_POST);
var_dump('por queeeeeee');
echo "</pre>";
$error = 0;
if (   (! isset ($_POST["tag"])  )
    || (! isset ($_POST["description"]) )
    || (! isset ($_POST["parent"]) )) {
    $error = 1;
}

if(!$error){
	$dbclient = new DBClient($config["db_config"]) or die ($dbclient->lq_error());
	$dbclient->connect() or die ($dbclient->lq_error());
	$q = "insert into groups (tag, description) values ('" . $_POST["tag"] . "', '" . $_POST["description"] . "');";
	$dbclient->exeq($q) or die($dbclient->lq_error());
	$dbclient->disconnect();
?>
	<script type="text/javascript">location.reload();</script>
<?php
}
?>