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

require_once (__DIR__ . "/include/config.php");
require_once (__DIR__ . "/lib/db.php");
require_once (__DIR__ . "/include/functions_util.php");
require_once (__DIR__ . "/lib/coduser.php");

try {
    $auth_level_required = get_required_auth_level('','cliupdate','');
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

if ((!isset($_POST["u"]))
    || (!isset($_POST["p"]))
    || (!isset($_POST["h"])))
    die ("ERR");
if   ( ( strlen($_POST["u"]) < MIN_USER_LENGTH )
    || ( strlen($_POST["p"]) < MIN_PASS_LENGTH )
    || ( strlen($_POST["h"]) < MIN_HOST_LENGTH ))
    die ("ERR");


$dbclient = $config["dbh"];

$rq_user = $dbclient->prepare($_POST["u"], "email");
$rq_pass = base64_decode($_POST["p"]);

if ($user->login($rq_user, $rq_pass) == null ) {
    echo "ERR: Please register at http://" . $config["domainname"];
    exit (3);
}


$phost = $dbclient->prepare($_POST["h"], "url_get");
$fields = explode(".", $phost,2);
$host   = $fields[0];
$domain = $fields[1];


// Check if user has grants to edit that host
if (! $user->check_grant_over_item("read", $phost)){
    die ("ERR: No grants over this item");
}

if(   ( strlen($host) < MIN_HOST_LENGTH )
   || ( strlen($host) > MAX_HOST_LENGTH )){

    die ("ERR: nombre de host no valido");
}

$host =  $dbclient->prepare($host, "letters") . "." . $domain;

// 1- CHECK ACTUAL IP
$q="select ip,ttl from hosts where oid=(select id from users where mail='" . $rq_user . "') and tag='" . $host . "';";
$r = $dbclient->fetch_object( $dbclient->exeq($q) );
if ( $dbclient->lq_nresults() == 0 ) {
    die ("ERR: Ese host no esta registrado, confirme en http://" . $domain );
}
if ( $r->ip != ip2long(_ip()) ){
	$ip  = _ip();
    $iip = ip2long($ip);
    $ttl = $r->ttl;
    // 2- UPDATE IF NECESSARY
    $q="update hosts set ip='" . $iip . "', last_updated=now() where oid=(select id from users where mail='" . $rq_user . "') and tag='" . $host . "';";
    $dbclient->exeq($q);

    // LAUNCH DNS UPDATER erase + add
    $out = shell_exec("dnsmgr d " . $host . " A " . $ip);
    $out = shell_exec("dnsmgr a " . $host . " A " . $ip . " " . $ttl);

    echo "OK: " . $host . " actualizado a " . $ip  . " " . $out . "\n";
}
else{
    echo "OK: La ip asociada a " . $host . " ya estaba actualizada.\n";
}

?>
