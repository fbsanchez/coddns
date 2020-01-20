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

require_once (__DIR__ . "/../../include/config.php");
require_once (__DIR__ . "/../../lib/db.php");
require_once (__DIR__ . "/../../include/functions_ip.php");
require_once (__DIR__ . "/../../include/functions_util.php");
require_once (__DIR__ . "/../../lib/coduser.php");

try {
    $auth_level_required = get_required_auth_level('usr','hosts','rq_new');
    $user = new CODUser();
    $user->check_auth_level($auth_level_required);
}
catch (Exception $e) {
    echo $e->getMessage();
    exit (1);
}

session_start();
if (!isset($_SESSION["lan"])){
    $_SESSION["lan"] = "es";
}
$lan = $_SESSION["lan"];
session_write_close();


putenv("CODDNS_RNDC_KEY=" . $config["rndc_key"]);


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


function add_referenced_host($dbclient, $host, $rtype_p, $rtag, $ttl,  $gid = 0, $priority = 10){
    global $text,$lan;

    // INSERT NEW HOST IF NO ONE EXISTS
    $q = "select * from hosts where lower(tag)=lower('" . $host . "');";
    $dbclient->exeq($q);

    if( $dbclient->lq_nresults() > 0 )
        die ("Ese nombre de host no est&aacute; disponible");

    // Confirm rtag previously exists
    $q = "select * from hosts where lower(tag)=lower('" . $rtag . "');";
    $dbclient->exeq($q);

    $is_external_reference = 1;
    if( $dbclient->lq_nresults()  == 0 ) {
        $is_external_reference = 0;
    }
        

    // LAUNCH DNS UPDATER
    switch ($rtype_p) {
        case "MX":
            $out = shell_exec("dnsmgr a " . $host . " " . $rtype_p . " " . $rtag . " " . $ttl . " " . $priority);
            break;
        default:
            $out = shell_exec("dnsmgr a " . $host . " " . $rtype_p . " " . $rtag . " " . $ttl);
    }

    if (preg_match("/ERR/", $out)) {
        echo $text[$lan]["err_i"] . "<br> [" .  $out . "] ";
    }
    else {
        if ($is_external_reference == 0) {
            $q = "insert into hosts (oid, tag, rid, ttl, rtype, gid) values ( (select id from users where mail=lower('" . $_SESSION["email"] . "')), lower('" . $host . "'), (select id from hosts h where lower(tag)=lower('" . $rtag . "')), $ttl, (select id from record_types where tag ='". $rtype_p ."'), $gid);";
        }
        else {
            $q = "insert into hosts (oid, tag, rtag, ttl, rtype, gid) values ( (select id from users where mail=lower('" . $_SESSION["email"] . "')), lower('" . $host . "'), lower('" . $rtag . "'), $ttl, (select id from record_types where tag ='". $rtype_p ."'), $gid);";
        }

        $dbclient->exeq($q) or die($dbclient->lq_error());
    }
    session_write_close();
}
?>

<!DOCTYPE html>

<html>
<head>
    <title><?php echo $text[$lan]["title"]; ?></title>
</head>

<body>
<?php

$error = 0;
if (   (! isset ($_POST["h"])  )
    || (! isset ($_POST["ip"]) )
    || (! isset ($_POST["rtype"])
    || (! isset ($_POST["zone"]) ) )) {
    $error = 1;
}
if (   ( $_POST["rtype"] == "A")
    &&(
         ( strlen ($_POST["h"])  < MIN_HOST_LENGTH)
      || ( strlen ($_POST["h"])  > MAX_HOST_LENGTH)
      || ( strlen ($_POST["ip"]) < 7)
      || ( !preg_match('/^[a-zA-Z]+([0-9]*[a-zA-Z]*)*$/',$_POST["h"])) )
    ) {
    $error = 1;
}

elseif (   ( ( $_POST["rtype"] == "CNAME")
          || ( $_POST["rtype"] == "NS")
          || ( $_POST["rtype"] == "MX") )
        &&(
             ( strlen ($_POST["h"])  < MIN_HOST_LENGTH)
          || ( strlen ($_POST["h"])  > MAX_HOST_LENGTH)
          || ( strlen ($_POST["rtag_" . $_POST["rtype"]]) < 7)
          || ( !preg_match('/^[a-zA-Z]+([0-9]*[a-zA-Z]*)*$/',$_POST["h"])) )
        ) {
    $error = 1;
}

if ($error === 1) {
?>
    <p><?php echo $text[$lan]["err_f"]; ?></p>
    <a href="<?php echo $config["html_root"];?>/?lang=<?php echo $lan;?>"><?php echo $text[$lan]["back"];?></a>
<?php
    exit (1);
}

$dbclient = $config["dbh"];



$zone     = $dbclient->prepare($_POST["zone"], "url_get");

// XXX check if user has grants to write in the public section, maybe remove left side of the query is better than always allow users to write in public areas...
// check granted zones for the current user
$q = "select t.id, t.domain from ((select z.id, z.domain from zones z, tusers_groups ug, users u where z.gid=ug.gid and ug.oid=u.id and mail='" . $_SESSION["email"] . "' and (ug.edit=1 or ug.admin=1)) "
        . "UNION (select z.id, z.domain from zones z, tusers_groups ug, users u where z.is_public=1 and (ug.edit=1 or ug.admin=1))) t where t.domain='" . $zone . "';";

//echo $q;

$results  = $dbclient->exeq($q);
$r = $dbclient->fetch_object($results);

$zone_id = -1;
if (isset ($r->id)){
    $zone_id = $r->id;
}
else {
    $error = 1;
}

$host     = $dbclient->prepare($_POST["h"], "letters") . "." . $zone;
$rtype_p  = $dbclient->prepare($_POST["rtype"], "letters");
$ttl      = $dbclient->prepare($_POST["ttl"], "number");
$group    = $dbclient->prepare($_POST["group"], "letters");

if ($rtype_p != "A"){
    $rtag     = $dbclient->prepare($_POST["rtag_" . $rtype_p], "url_get");
}

if ($error === 1) {
?>
    <p><?php echo $text[$lan]["err_f"]; ?></p>
    <a href="<?php echo $config["html_root"];?>/?lang=<?php echo $lan;?>"><?php echo $text[$lan]["back"];?></a>
<?php
    exit (1);
}


// INSERT THE REGISTER IN THE DB AND IN THE SELECTED ZONE

// Check received group
$q = "select id from groups where lower(tag)=lower('" . $group . "');";
$rs = $dbclient->exeq($q);
if( $dbclient->lq_nresults() <= 0 ) 
    die ("Ese grupo no est&aacute; disponible.");
$r = $dbclient->fetch_object($rs);
$gid = $r->id;

switch ($rtype_p){
    case "A": {
        $check = ip2long($_POST["ip"]);
        if ( $check < 0 || $check == FALSE ){
            echo $text["en"]["ip_f"];
            exit (2);
        }
        $ip       = filter_var($_POST["ip"], FILTER_VALIDATE_IP);
        $iip      = $dbclient->prepare($ip, "ip");
        
        if ($ip === FALSE){
            echo $text["en"]["ip_f"];
            exit (1);
        }
        
        // INSERT NEW HOST IF NO ONE EXISTS
        $q = "select * from hosts where lower(tag)=lower('" . $host . "');";
        $dbclient->exeq($q);

        if( $dbclient->lq_nresults() > 0 )
            die ("Ese nombre de host no est&aacute; disponible");

        // LAUNCH DNS UPDATER
        $out = shell_exec("dnsmgr a " . $host . " A " . $ip . " " . $ttl);

        if (preg_match("/ERR/", $out)) {
            echo $text[$lan]["err_i"] . "<br> [" .  $out . "] ";
        }
        else {
            $q = "insert into hosts (oid, tag, ip, ttl, rtype, zone_id, gid) values ( (select id from users where mail=lower('" . $_SESSION["email"] . "')), lower('" . $host . "'), $iip, $ttl, (select id from record_types where tag ='". $rtype_p ."'), $zone_id, $gid);";
			$dbclient->exeq($q) or die($dbclient->lq_error());
			echo $text[$lan]["ok"]; 
        }
        session_write_close();
        break;
    }
    case "CNAME":{
        echo "Adding $host as CNAME of $rtag<br>";
        add_referenced_host($dbclient, $host, $rtype_p, $rtag, $ttl, $gid);

        break;
    }
    case "NS":{
        echo "Adding NS target<br>";
        add_referenced_host($dbclient, $host, $rtype_p, $rtag, $ttl, $gid);
        break;
    }
    case "MX":{
        echo "Adding MX target<br>";
        add_referenced_host($dbclient, $host, $rtype_p, $rtag, $ttl, $gid);
        break;
    }
    default:{
        echo "Unknown RR<br>";
        break;
    }
}
?>
<a class="ajax_button" href="<?php echo $config["html_root"] . "/?m=usr&z=hosts" ?>">OK</a>
</body>

</html>

