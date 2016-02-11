<?php

require_once(dirname(__FILE__) . "/../include/config.php");
require_once(dirname(__FILE__) . "/../lib/util.php");
require_once(dirname(__FILE__) . "/../lib/db.php");

check_user_auth();

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
?>
</body>
</html>
