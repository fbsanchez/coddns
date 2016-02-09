<?php

require_once(dirname(__FILE__) . "/../include/config.php");
require_once(dirname(__FILE__) . "/../lib/db.php");
require_once(dirname(__FILE__) . "/../lib/util.php");
require_once(dirname(__FILE__) . "/../lib/coduser.php");

$user = new CODUser();
$user->check_auth_level(100); // Require administrator access

if (! defined("_VALID_ACCESS")) {
    header ("Location: " . $config["html_root"] . "/");
    exit (1);
}
?>


<!DOCTYPE HTML>

<html>
<head>
</head>

<body>
	<section>
		<h2>Panel de administraci&oacute;n</h2>
		<p>En construcci&oacute;n</p>
	</section>
</body>

</html>