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
	$auth_level_required = get_required_auth_level('adm','site','rq_new_group');
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
	$dbclient = $dbclient = $config["dbh"];

	$l = "select id from groups where tag ='" . $_POST["tag"] . "';";
	$s = $dbclient->exeq($l) or die ($dbclient->lq_error());
	$error = 0; 
	if($dbclient->fetch_array ($s)){
		$error = 1;
		$message = $_POST["tag"];
	}
}

if(!$error){
	if ( ($_POST["parent"] == -1)){
		$q = "insert into groups (tag, description) values ('" . $_POST["tag"] . "', '" . $_POST["description"] . "');";
	}
	else{
		$q = "insert into groups (tag, description, parent) values ('" . $_POST["tag"] . "', '" . $_POST["description"] . "', '" . $_POST["parent"] . "');";
	}
	$dbclient->exeq($q) or die($dbclient->lq_error());
	?>
		<script type="text/javascript">location.reload();</script>
	<?php
}
else{
	if( isset($message) ){
		echo "<h4 class='message_error'>El nombre '" . $message . "' ya existe en la bbdd por favor introduzca otro</h4>";
	}
}
?>