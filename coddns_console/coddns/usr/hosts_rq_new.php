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

require_once (dirname(__FILE__) . "/../include/config.php");
require_once (dirname(__FILE__) . "/../lib/db.php");
require_once (dirname(__FILE__) . "/../lib/ipv4.php");
require_once (dirname(__FILE__) . "/../lib/util.php");
require_once (dirname(__FILE__) . "/../lib/coduser.php");

$auth_level_required = get_required_auth_level('usr','hosts','rq_new');
$user = new CODUser();
$user->check_auth_level($auth_level_required);

session_start();
if (!isset($_SESSION["lan"])){
    $_SESSION["lan"] = "es";
}
$lan = $_SESSION["lan"];
session_write_close();



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
    || ( strlen ($_POST["h"])  < MIN_HOST_LENGTH)
    || ( strlen ($_POST["h"])  > MAX_HOST_LENGTH)
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

$host     = $dbclient->prepare($_POST["h"], "letters") . "." . $config["domainname"];
$ip       = filter_var($_POST["ip"], FILTER_VALIDATE_IP);
$iip      = $dbclient->prepare($ip, "ip");
$ttl      = $dbclient->prepare($_POST["ttl"], "number");
$rtype_p  = $dbclient->prepare($_POST["rtype"], "letters");

$dbclient->connect() or die ($dbclient->lq_error());
if ($ip === FALSE){
    echo $text["en"]["ip_f"];
    exit (1);
}

// INSERT NEW HOST IF NO ONE EXISTS
$q = "select * from hosts where lower(tag)=lower('" . $host . "');";
$dbclient->exeq($q);

if( $dbclient->lq_nresults() > 0 )
    die ("Ese nombre de host no est&aacute; disponible<br><a href='/'>Volver</a>");

// LAUNCH DNS UPDATER
$out = shell_exec("/opt/ddns/dnsmgr.sh a " . $host . " A " . $ip . " " . $ttl);

$q = "insert into hosts (oid, tag, ip, ttl, rtype) values ( (select id from users where mail=lower('" . $_SESSION["email"] . "')), lower('" . $host . "'), $iip, $ttl, (select id from record_types where tag ='". $rtype_p ."'));";
$dbclient->exeq($q) or die($dbclient->lq_error());

$dbclient->disconnect();
session_write_close();

if (preg_match("/ERR/", $out)) {
    echo $text[$lan]["err_i"] . "<br> [" .  $out . "] ";
}
else {
    echo $text[$lan]["ok"];
?>
    <script type="text/javascript">location.reload();</script>
    <?php
}

?>
</body>

</html>

