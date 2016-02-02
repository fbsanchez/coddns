<?php

require_once(dirname(__FILE__) . "/../include/config.php");
require_once(dirname(__FILE__) . "/../lib/db.php");

session_start();
if (!isset($_SESSION["lan"])){
	$_SESSION["lan"] = "es";
}
$lan = $_SESSION["lan"];
session_write_close();

if (! defined("_VALID_ACCESS")) {
	header ("Location: " . $config["html_root"] . "/?lang=" . $lan);
    exit (1);
}

?>
<!DOCTYPE html>

<html>

<head>
<title>Eliminar un host</title>
<meta charset="UTF-8">
<style type="text/css">
</style>

</head>
<body>
<?php
if(! isset($_SESSION["email"]) ){
    header ("Location: " . $config["html_root"]);
    exit (1);
}


if (! isset ($_POST["delh"]) ){
	die ("woops...");
}

$dbclient = new DBClient($db_config);

$dbclient->connect() or die("ERR");

$host = strtok($_POST["delh"],".");
$host = $dbclient->prepare($host, "letters") . "." . $config["domainname"];

$q = "delete from hosts where oid=(select id from users where lower(mail)=lower('" . $_SESSION["email"] . "')) and lower(tag)=lower('" . $host . "');";
$dbclient->exeq($q);


// LAUNCH DNS UPDATER
$out = shell_exec("dnsmgr d " . $host . " A");

$dbclient->disconnect();

?>

<?php
if (! strlen($out) > 0) {
    header("Location: " . $config["html_root"] . "/?z=hosts&lang=" . $lan);
}
echo "<div><p>Se ha eliminado " . $host . " correctamente<p><br><a href=\"" . $config["html_root"] . "/?z=hosts&lang=" . $lan . "\">Volver</a></div>";

?>

</body>
</html>