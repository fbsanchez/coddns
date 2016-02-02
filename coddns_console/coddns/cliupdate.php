<?php
require_once ("include/config.php");
require_once ("lib/ipv4.php");
require_once ("lib/db.php");

defined ("LENGTH_USER_MIN") or define ("LENGTH_USER_MIN", 2);
defined ("LENGTH_PASS_MIN") or define ("LENGTH_PASS_MIN", 2);
defined ("LENGTH_HOST_MIN") or define ("LENGTH_HOST_MIN", 1);
defined ("LENGTH_HOST_MAX") or define ("LENGTH_HOST_MAX", 200);

if ((!isset($_POST["u"]))
    || (!isset($_POST["p"]))
    || (!isset($_POST["h"])))
    die ("ERR");
if   ( ( strlen($_POST["u"]) < LENGTH_USER_MIN )
    || ( strlen($_POST["p"]) < LENGTH_PASS_MIN )
    || ( strlen($_POST["h"]) < LENGTH_HOST_MIN ))
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
    || ( strlen($host) < LENGTH_HOST_MIN )
    || ( strlen($host) > LENGTH_HOST_MAX ))
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
