<?php
require_once (dirname(__FILE__) . "/../include/config.php");
require_once (dirname(__FILE__) . "/../lib/db.php");
require_once (dirname(__FILE__) . "/../lib/ipv4.php");

//check_user_auth();

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
$text["es"]["err_i"] = "Error interno, verifique los mensajes y la configuraci&oacute;n<br>";
$text["es"]["ok"]    = "Agregado correctamente";

/* ENGLISH */
$text["en"]["title"] = "Add a new host";
$text["en"]["err_f"] = "Please, fill the data and accomplish the restrictions.";
$text["en"]["back"]  = "Go back";
$text["en"]["ip_f"]  = "The IP address is not valid";
$text["en"]["err_i"] = "Internal error, please check the messages and the configuration<br>";
$text["en"]["ok"]    = "Succesfully added<script>r();</script>";

?>

<!DOCTYPE html>

<html>
<head>
    <title><?php echo $text[$lan]["title"]; ?></title>
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

$dbclient= new DBClient($db_config);
$dbclient->connect() or die ("ERR");



$host = $dbclient->prepare($_POST["h"], "letters") . "." . $config["domainname"];
$ip   = $_POST["ip"];

// INSERT NEW HOST IF NO ONE EXISTS
$q = "select * from hosts where lower(tag)=lower('" . $host . "');";
$dbclient->exeq($q);

if( $dbclient->lq_nresults() > 0 )
    die ("Ese nombre de host no est&aacute; disponible<br><a href='/'>Volver</a>");

// LAUNCH DNS UPDATER
$out = shell_exec("/opt/ddns/dnsmgr.sh a " . $host . " A " . $ip);

$q = "insert into hosts (oid, tag, ip) values ( (select id from users where mail=lower('" . $_SESSION["email"] . "')), lower('" . $host . "'), INET_ATON('" . $ip . "') );";
$dbclient->exeq($q);

$dbclient->disconnect();
session_write_close();

if (preg_match("/ERR/", $out)) {
    echo $text[$lan]["err_i"] . "<br> [" .  $out . "] ";
}
else {
    echo $text[$lan]["ok"];
//    header ("Location: " . $config["html_root"] . "/");
}

?>
</body>

</html>

