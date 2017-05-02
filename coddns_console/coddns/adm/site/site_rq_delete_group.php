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
require_once (__DIR__ . "/../../include/functions_ip.php");
require_once (__DIR__ . "/../../include/functions_util.php");
require_once (__DIR__ . "/../../lib/coduser.php");

try {
	$auth_level_required = get_required_auth_level('adm','site','rq_delete_group');
	$user = new CODUser();
	$user->check_auth_level($auth_level_required);
}
catch (Exception $e) {
	echo $e->getMessage();
	exit (1);
}

$error = 0;
if (   (! isset ($_POST["tag"]) )   ) {
    $error = 1;
}
else{
	$dbclient = $config["dbh"];
	if(  ( isset ($_POST["delete_all"]) )  ){
		//delete all
		$tag_explode = explode(",",$_POST["tag"]);
		foreach ($tag_explode as $key => $value) {
			
			$l = "SELECT id from groups WHERE tag ='" . $value . "';";
			$q = $dbclient->exeq($l) or die ($dbclient->lq_error());
			$row = $dbclient->fetch_object ($q);

			$l = "DELETE FROM groups WHERE id ='" . $row->id . "';";
			$dbclient->exeq($l) or die ($dbclient->lq_error());
		}
	}
	else{
		//normal delete
		$result = array();
		
		$l = "SELECT id from groups WHERE tag ='" . $_POST["tag"] . "';";
		$q = $dbclient->exeq($l) or die ($dbclient->lq_error());
		$row = $dbclient->fetch_object ($q);

		$l = "DELETE FROM groups WHERE id ='" . $row->id . "';";
		$dbclient->exeq($l) or die ($dbclient->lq_error());
		
	}
	?>
		<script type="text/javascript">location.reload();</script>
	<?php
}

?>