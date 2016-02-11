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

require_once (dirname(__FILE__) . "/include/config.php");
require_once (dirname(__FILE__) . "/lib/db.php");
require_once (dirname(__FILE__) . "/lib/util.php");
require_once (dirname(__FILE__) . "/lib/coduser.php");

$auth_level_required = get_required_auth_level('','cliupdate','');
$user = new CODUser();
$user->check_auth_level($auth_level_required);

session_start();
if (!isset($_SESSION["lan"])){
    $_SESSION["lan"] = "es";
}
$lan = $_SESSION["lan"];
session_write_close();

defined ("MIN_USER_LENGTH") or define ("MIN_USER_LENGTH", 4);
defined ("MIN_PASS_LENGTH") or define ("MIN_PASS_LENGTH", 4);
defined ("MIN_HOST_LENGTH") or define ("MIN_HOST_LENGTH", 1);
defined ("MAX_HOST_LENGTH") or define ("MAX_HOST_LENGTH", 200);

if ((!isset($_POST["u"]))
    || (!isset($_POST["p"]))
    || (!isset($_POST["h"])))
    die ("ERR");
if   ( ( strlen($_POST["u"]) < MIN_USER_LENGTH )
    || ( strlen($_POST["p"]) < MIN_PASS_LENGTH )
    || ( strlen($_POST["h"]) < MIN_HOST_LENGTH ))
    die ("ERR");


$dbclient = new DBClient($db_config);

$dbclient->connect() or die ("ERR");

$user = $dbclient->prepare($_POST["u"], "email");
$rq_pass = base64_decode($_POST["p"]);
$pass = hash ("sha512",$salt . $rq_pass);

$host = strtok($_POST["h"],".");
$main = strtok(".");
$dom  = strtok(".");

$check = $config["domainname"];
$checkm = strtok ($check, ".");
$checkd = strtok (".");

if(    ( $main != $checkm )
    || ( $dom  != $checkd )
    || ( strlen($host) < MIN_HOST_LENGTH )
    || ( strlen($host) > MAX_HOST_LENGTH ))
    die ("ERR: nombre de host no valido");
$host =  $dbclient->prepare($host, "letters") . "." . $config["domainname"];

$q="select * from users where mail='" . $user . "' and pass='" . $pass . "';";
$dbclient->exeq($q);

if( $dbclient->lq_nresults() == 0 ) {/* no user */
//    error_log("[TRICK]: Lanzo query de insert...");
//    error_log("[TRICK]: Agregado usuario " . $user .  " from  " .  _ip());
    die ("ERR: Registrese en http://" . $config["domainname"]);
}
else {

    // 1- CHECK ACTUAL IP
    $q="select ip from hosts where oid=(select id from users where mail='" . $user . "') and tag='" . $host . "';";
    $r = pg_fetch_object( $dbclient->exeq($q) );
    if ( $dbclient->lq_nresults() == 0 ) {
        die ("ERR: Ese host no esta registrado, confirme en http://" . $check . "." . $checkd );
    }
    if ( $r->ip != ip2long(_ip()) ){
		$ip  = _ip();
        $iip = long2ip($ip);
        // 2- UPDATE IF NECESSARY
        $q="update hosts set ip='" . $iip . "', last_updated=now() where oid=(select id from users where mail='" . $user . "') and tag='" . $host . "';";
        $dbclient->exeq($q);

        // LAUNCH DNS UPDATER erase + add
        $out = shell_exec("dnsmgr d " . $host . " A " . $ip);
        $out = shell_exec("dnsmgr a " . $host . " A " . $ip);

        echo "OK: " . $host . " actualizado a " . $ip . $out . "\n";
    }
    else{
        echo "OK: La ip asociada a " . $host . " ya estaba actualizada.\n";
    }

}
$dbclient->disconnect();
?>
