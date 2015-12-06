<?php
require_once("include/config.php");
require_once("lib/pgclient.php");
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

$pgclient = new PgClient($db_config);

$pgclient->connect() or die("ERR");

$host = strtok($_POST["delh"],".");
$host =  $pgclient->prepare($host, "letters") . "." . $config["domainname"];

$q = "delete from hosts where oid=(select id from usuarios where lower(mail)=lower('" . $_SESSION["email"] . "')) and lower(tag)=lower('" . $host . "');";
$pgclient->exeq($q);


// LAUNCH DNS UPDATER
$out = shell_exec("dnsmgr d " . $host . " A");


$pgclient->disconnect();

echo "<div><p>Se ha eliminado " . $host . " correctamente<p><a href='" . $config["html_root"] . "/'>Volver</a></div>";

session_write_close();
?>
</body>

</html>

<?php
if (! strlen($out) > 0)
    header("Location: " . $config["html_root"]);

?>
