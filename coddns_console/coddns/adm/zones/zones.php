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

$auth_level_required = get_required_auth_level('adm','zones', null);
$user = new CODUser();
$user->check_auth_level($auth_level_required);

?>


<!DOCTYPE HTML>

<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo $config["html_root"] . "/rs/css/pc/service_status.css";?>" />
</head>
<body>

<?php

$dbclient = new DBClient($config["db_config"]) or die ($dbclient->lq_error());


$zones = $dbclient->get_sql_array("select z.*, s.tag as server_tag from zones z, servers s where z.server_id=s.id;");

?>
<table class="">
<tr>
	<th>Zona</th><th>Servidor</th><th>Acceso p&uacute;blico</th>
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
