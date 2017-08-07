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
require_once(__DIR__ . "/../../include/functions_util.php");
require_once(__DIR__ . "/../../lib/coduser.php");

try {
	$auth_level_required = get_required_auth_level('adm','zones', null);
	$user = new CODUser();
	$user->check_auth_level($auth_level_required);
}
catch (Exception $e) {
	echo $e->getMessage();
	exit (1);
}

?>


<!DOCTYPE HTML>

<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo $config["html_root"] . "/rs/css/" . $config["html_view"]; ?>/zones.css" />
</head>
<body>




<?php
// links to new zone formulary

// Show zones in table view, as cards
?>


<?php

$dbclient = $config["dbh"];


$zones = $dbclient->get_sql_array("select z.*, s.tag as server_tag from zones z, servers s, zone_server zs where zs.id_server=s.id and zs.id_zone=z.id;");


// Zone list
// Steps to create a zone:
//   1- Define a zone structure file
//   2- Link zone to server
//   3- configure grants over the zone
?>
<table class="">
<tr>
	<th>Zone</th><th>Servidor</th><th>Acceso p&uacute;blico</th>
</tr>
<?php
foreach ($zones["data"] as $zone) {
	echo "<tr><td>" . $zone["domain"] . "</td><td>" . $zone["server_tag"] . "</td><td>" . $zone["is_public"] . "</td></tr>";
}
echo "</table>";

?>

En construcci&oacute;n
</body>

</html>
