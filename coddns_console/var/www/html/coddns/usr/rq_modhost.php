<?php
require_once ("../include/config.php");
require_once ("../lib/pgclient.php");


defined ("LENGTH_USER_MIN") or define ("LENGTH_USER_MIN", 2);
defined ("LENGTH_PASS_MIN") or define ("LENGTH_PASS_MIN", 2);
defined ("LENGTH_HOST_MIN") or define ("LENGTH_HOST_MIN", 1);
defined ("LENGTH_HOST_MAX") or define ("LENGTH_HOST_MAX", 200);

session_start();
if(! isset ($_SESSION["email"])){
    header ("Location: /");
    exit (1);
}

if ( (! isset ($_POST["edith"])) || (! isset($_POST["nip"])) ){
    echo "Rellene todos los datos";
    exit (1);
}

if (   ( strlen($_POST["edith"]) < LENGTH_HOST_MIN)
    || ( strlen($_POST["nip"]) < 7) ){
    echo "Rellene todos los datos y respete las longitudes m&aacute;ximas.";
    exit (1);
}
$check = ip2long($_POST["nip"]);
if ( $check < 0 || $check == FALSE ){
    echo "La direcci&oacute;n IP no es v&aacute;lida";
    exit (2);
}


$pgclient = new PgClient($db_config);
$pgclient->connect() or die ("ERR");

$host = strtok($_POST["edith"],".");
$main = strtok(".");
$dom  = strtok(".");

$check  = $config["domainname"];
$checkm = strtok($check,".");
$checkd = strtok(".");

if(    ( $main != $checkm )
    || ( $dom  != $checkd  )
    || ( strlen($host) < LENGTH_HOST_MIN )
    || ( strlen($host) > LENGTH_HOST_MAX ))
    die ("ERR: nombre de host no valido");
$host =  $pgclient->prepare($host, "letters") . "." . $config["domainname"];
$ip   = $_POST["nip"];

// UPDATE ONLY AN EXISTENT HOST
$q = "select count(tag) from hosts where lower(tag)=lower('" . $host . "') and oid=(select id from usuarios where lower(mail)=lower('" . $pgclient->prepare($_SESSION["email"],"email") . "'));";
$pgclient->exeq($q);

if( $pgclient->lq_nresults() == 1 ){
    $q = "update hosts set ip='" . $ip . "' where tag='" . $host . "';";
    $pgclient->exeq($q);

    // LAUNCH DNS UPDATER
    // -- erase
    $out = shell_exec("dnsmgr d " . $host . " A");
    // -- add
    $out = shell_exec("dnsmgr a " . $host . " A " . $ip);
    echo "OK";
}
else{
    header ("Location: /err403.html");
    echo "ERR";
    exit (3);
}



$pgclient->disconnect();
session_write_close();

header ("Location: /");
?>
