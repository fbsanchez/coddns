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
require_once (dirname(__FILE__) . "/../lib/util.php");
require_once (dirname(__FILE__) . "/../lib/coduser.php");

$auth_level_required = get_required_auth_level('usr','hosts','rq_mod');
$user = new CODUser();
$user->check_auth_level($auth_level_required);

session_start();
if (!isset($_SESSION["lan"])){
    $_SESSION["lan"] = "es";
}
$lan = $_SESSION["lan"];
session_write_close();

if ( (! isset ($_POST["edith"])) || (! isset($_POST["nip"])) ){
    echo "Rellene todos los datos";
    exit (1);
}

if (   ( strlen($_POST["edith"]) < MIN_HOST_LENGTH)
    || ( strlen($_POST["nip"]) < 7) ){
    echo "Rellene todos los datos y respete las longitudes m&aacute;ximas.";
    exit (1);
}
$check = ip2long($_POST["nip"]);
if ( $check < 0 || $check == FALSE ){
    echo "La direcci&oacute;n IP no es v&aacute;lida";
    exit (2);
}


$dbclient = new DBClient($db_config);
$dbclient->connect() or die ("ERR");

$host = strtok($_POST["edith"],".");
$main = strtok(".");
$dom  = strtok(".");

$check  = $config["domainname"];
$checkm = strtok($check,".");
$checkd = strtok(".");

if(    ( $main != $checkm )
    || ( $dom  != $checkd  )
    || ( strlen($host) < MIN_HOST_LENGTH )
    || ( strlen($host) > MAX_HOST_LENGTH ))
    die ("ERR: nombre de host no valido");
$host =  $dbclient->prepare($host, "letters") . "." . $config["domainname"];
$ip   = $dbclient->prepare($_POST["nip"], "ip");

if ($ip === FALSE){
    echo $text["en"]["ip_f"];
    exit (1);
}

// UPDATE ONLY AN EXISTENT HOST
$q = "select count(tag) from hosts where lower(tag)=lower('" . $host . "') and oid=(select id from users where lower(mail)=lower('" . $dbclient->prepare($_SESSION["email"],"email") . "'));";
$dbclient->exeq($q);

if( $dbclient->lq_nresults() == 1 ){
    $q = "update hosts set ip=$ip where tag='" . $host . "';";
    $dbclient->exeq($q);

    // LAUNCH DNS UPDATER
    // -- erase
    $out = shell_exec("dnsmgr d " . $host . " A");
    // -- add
    $out = shell_exec("dnsmgr a " . $host . " A " . $ip);
    echo "OK";
}
else{
    echo "ERR";
    redirect ("Location: " . $config["html_root"] . "/?z=err40X.html");
    exit (3);
}



$dbclient->disconnect();

//header ("Location: ". $config["html_root"] . "/?z=hosts&lang=". $lan);
?>
<script type="text/javascript">location.reload();</script>
