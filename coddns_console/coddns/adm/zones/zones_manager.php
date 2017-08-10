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
require_once(__DIR__ . "/../../include/functions_zone.php");
require_once(__DIR__ . "/../../lib/coduser.php");

if (! defined("_VALID_ACCESS")) { // Avoid direct access
    header ("Location: " . $config["html_root"] . "/");
    exit (1);
}

try {
	$auth_level_required = get_required_auth_level('adm','zones','manager');
	$user = new CODUser();
	$user->check_auth_level($auth_level_required);
}
catch (Exception $e) {
	echo $e->getMessage();
	exit (1);
}


if(!isset($servername)){
	$domain = secure_get("id");
}

$zone = get_zone_from_domain($domain);
?>
<!DOCTYPE html>
<html>
<head>

</head>
<body>
<section>
	<h2>Zone manager</h2>
	<?php

echo "<pre>";
var_dump($zone);
echo "</pre>";

	?>
	<a class="return" href="<?php echo $config["html_root"] . "/?m=adm&z=center#zones" ?>">Go back</a>
</section>
</body>
</html>

