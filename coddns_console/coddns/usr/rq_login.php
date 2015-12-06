<?php
require_once ("../include/config.php");
require_once ("../lib/ipv4.php");
require_once ("../lib/pgclient.php");

session_start();

if( !isset($_SESSION["lan"]) ){
    session_write_close();
    header ("Location: /?lang=es");
    exit (1);
}

$lan = $_SESSION["lan"];

/* CASTELLANO */
$text["es"]["err1"] = "<div class='err'>Rellene todos los datos</div>";
$text["es"]["err2"] = "<div class='err'>No cumple las longitudes minimas</div>";
$text["es"]["err3"] = "<div class='err'>Los datos no son correctos.</div>";
$text["es"]["dberror"] = "<div class='err'>Woooops, contacte con el administrador del sitio.</div>";
$text["es"]["welcome"] = "<div class='ok'>Bienvenido</div><script>location.reload();</script></div>";


/* ENGLISH */
$text["en"]["err1"] = "<div class='err'>Please fill all data</div>";
$text["en"]["err2"] = "<div class='err'>The data provided is not reaching the minimal length";
$text["en"]["err3"] = "<div class='err'>The data providen is not valid.</div>";
$text["en"]["dberror"] = "<div class='err'>Woooops, we have a problem! please contact the site administrator.</div>";
$text["en"]["welcome"] = "<div class='ok'>Welcome</div><script>location.reload();</script></div>";


/* DEUTSCH */

if ( (! isset($_POST["u"])) || (! isset($_POST["p"])) ){
    echo $text[$lan]["err1"];
    exit(1);
}

$rq_pass = base64_decode($_POST["p"]);

if ( ( strlen($_POST["u"]) < MIN_USER_LENGTH) || ( strlen($rq_pass) < MIN_PASS_LENGTH) ){
    echo $text[$lan]["err2"];
    exit(2);
}

$pgclient = new PgClient($db_config);
$user = $pgclient->prepare($_POST["u"], "email");
$pass = hash ("sha512",$salt . $rq_pass);

$pgclient->connect() or die ($text[$lan]["dberror"]);

$q = "Select * from usuarios where lower(mail)=lower('" . $user . "') and pass='" . $pass . "';";
$r = pg_fetch_object ($pgclient->exeq($q));
if ($pgclient->lq_nresults() == 0){ // USER NON EXISTENT OR PASSWORD ERROR
    echo $text[$lan]["err3"];
    exit (3);
}
$q = "update usuarios set last_login=now(), ip_last_login='" . _ip() . "' where lower(mail)=lower('" . $user . "');";
$pgclient->exeq($q) or die($text[$lan]["dberror"]);
$pgclient->disconnect();

$_SESSION["email"] = $user;
$_SESSION["time"]  = time();

session_write_close();
echo $text[$lan]["welcome"];
//header ("Location: /?lang=" . $lan . "&z=hosts");
?>

