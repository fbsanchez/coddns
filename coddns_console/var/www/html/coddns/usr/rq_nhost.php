<?php
require_once ("../include/config.php");
require_once ("../lib/pgclient.php");
require_once ("../lib/ipv4.php");

defined ("LENGTH_USER_MIN") or define ("LENGTH_USER_MIN", 2);
defined ("LENGTH_PASS_MIN") or define ("LENGTH_PASS_MIN", 2);
defined ("LENGTH_HOST_MIN") or define ("LENGTH_HOST_MIN", 1);
defined ("LENGTH_HOST_MAX") or define ("LENGTH_HOST_MAX", 200);

session_start();

if (! isset ($_SESSION["email"]) ){
    header ("Location: /" . $config["html_root"]);
    exit(1);
}

if( !isset($_SESSION["lan"]) ){
    session_write_close();
    header ("Location: /" . $config["html_root"] . "?lang=es");
    exit (1);
}

$lan = $_SESSION["lan"];

/* CASTELLANO */
$text["es"]["title"] = "Agregar un nuevo host";
$text["es"]["err_f"] = "Rellene todos los datos y respete las restricciones de longitud y formato.";
$text["es"]["back"]  = "Volver";
$text["es"]["ip_f"]  = "La direcci&oacute;n IP no es v&aacute;lida";
/* ENGLISH */
$text["en"]["title"] = "Add a new host";
$text["en"]["err_f"] = "Please, fill the data and accomplish the restrictions.";
$text["en"]["back"]  = "Go back";
$text["en"]["ip_f"]  = "The IP address is not valid";

?>

<!DOCTYPE html>

<html>
<head>
    <title><?php echo $text[$lan]["title"]; ?></title>
    <style type="text/css">
        body{width: 500px; padding: 10px;text-align: center; margin: 150px auto; border: 1px dashed #ddd; border-radius: 5px;}
    </style>
</head>

<body>
<?php

if (   (! isset ($_POST["h"])  )
    || (! isset ($_POST["ip"]) )
    || ( strlen ($_POST["h"])  < LENGTH_HOST_MIN)
    || ( strlen ($_POST["h"])  > LENGTH_HOST_MAX)
    || ( strlen ($_POST["ip"]) < 7)
    || ( !preg_match('/^[a-zA-Z]+([0-9]*[a-zA-Z]*)*$/',$_POST["h"])) ) {
?>
    <p><?php echo $text[$lan]["err_f"]; ?></p>
    <a href="<?php echo $config["html_root"];?>/?lang=<?php echo $lan;?>"><?php echo $text[$lan]["back"];?></a>
<?php
    exit (1);
}
$check = ip2long($_POST["ip"]);
if ( $check < 0 || $check == FALSE ){
    echo $text["en"]["ip_f"];
    exit (2);
}

$pgclient= new PgClient($db_config);
$pgclient->connect() or die ("ERR");



$host = $pgclient->prepare($_POST["h"], "letters") . "." . $config["domainname"];
$ip   = $_POST["ip"];

// INSERT NEW HOST IF NO ONE EXISTS
$q = "select * from hosts where lower(tag)=lower('" . $host . "');";
$pgclient->exeq($q);

if( $pgclient->lq_nresults() > 0 )
    die ("Ese nombre de host no est&aacute; disponible<br><a href='/'>Volver</a>");

// LAUNCH DNS UPDATER
$out = shell_exec("/opt/ddns/dnsmgr.sh a " . $host . " A " . $ip);

$q = "insert into hosts (oid, tag, ip) values ( (select id from usuarios where mail=lower('" . $_SESSION["email"] . "')), lower('" . $host . "'), '" . $ip . "');";
$pgclient->exeq($q);



echo "Agregado correctamente [" .  $out . "] ";
$pgclient->disconnect();
session_write_close();

if (! strlen($out) > 0)
    header ("Location: " . $config["html_root"]);


?>
</body>

</html>

