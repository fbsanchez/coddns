<?php
require_once("include/config.php");
require_once("lib/pgclient.php");

defined ("LENGTH_HOST_MIN") or define ("LENGTH_HOST_MIN", 1);
defined ("LENGTH_HOST_MAX") or define ("LENGTH_HOST_MAX", 200);

// devuelve la disponibilidad o no de una etiqueta host para un subdominio dado
if (! isset ($_POST["h"] )){
    header ("Location: /");
    exit(1);
}

$pgclient = new PgClient($db_config);

$pgclient->connect() or die("ERR");

$host = $pgclient->prepare($_POST["h"], "letters");

if (   ( strlen ($host) < LENGTH_HOST_MIN )
    || ( strlen ($host) > LENGTH_HOST_MAX )
    || ( !preg_match('/^[a-zA-Z]+([0-9]*[a-zA-Z]*)*$/',$_POST["h"])) ) {
    die ("<div class='r err'>No cumple los requisitos</div>");
}
$q = "select * from hosts where lower(tag)=lower('" . $host . "." . $config["domainname"] ."');";
$pgclient->exeq($q);
if ( $pgclient->lq_nresults() > 0 )
        echo "<div class='r err'>No disponible</div>";
else
        echo "<div class='r ok'>Disponible</div>";
$pgclient->disconnect();
?>

