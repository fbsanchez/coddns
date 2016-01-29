<?php
require_once (dirname(__FILE__) . "/../include/config.php");
require_once (dirname(__FILE__) . "/../lib/db.php");
require_once (dirname(__FILE__) . "/../lib/ipv4.php");

//check_user_auth();

session_start();
if (!isset($_SESSION["lan"])){
    $_SESSION["lan"] = "es";
}
$lan = $_SESSION["lan"];
session_write_close();

/* CASTELLANO */
/* ENGLISH */
/* DEUTSCH */

if ( (! isset($_POST["op"]))
  || (! isset($_POST["np"]))
  || (! isset($_POST["cp"])) ){
    echo "<div class'err'>Rellene todos los datos</div>";
    exit(1);
}

$rq_opass = (base64_decode($_POST["op"]));
$rq_npass = (base64_decode($_POST["np"]));
$rq_cpass = (base64_decode($_POST["cp"]));

if ( $rq_npass != $rq_cpass ) {
    echo "<div class='err'>La confirmacion no coincide</div>";
    exit(2);
}

if ( ( strlen($rq_opass) < MIN_PASS_LENGTH)
  || ( strlen($rq_npass) < MIN_PASS_LENGTH)
  || ( strlen($rq_cpass) < MIN_PASS_LENGTH) ){
    echo "<div class='err'>No cumple las longitudes m&iacute;nimas</div>";
    exit(2);
}

$pgclient = new PgClient($db_config);

$opass = hash ("sha512",$salt . $rq_opass);
$npass = hash ("sha512",$salt . $rq_npass);
$cpass = hash ("sha512",$salt . $rq_cpass);

$pgclient->connect() or die ("<div class='err'>Woooops, culpa nuestra, contacte con el administrador</div>");

$q = "Select * from usuarios where lower(mail)=lower('" . $_SESSION["email"] . "') and pass='" . $opass . "';";
$r = pg_fetch_object ($pgclient->exeq($q));
if ($pgclient->lq_nresults() == 0){ // USER NON EXISTENT OR PASSWORD ERROR
    echo "<div class='err'>Los datos introducidos no son correctos</div>";
    exit (3);
}
$q = "Update usuarios set pass='" . $npass . "' where lower(mail)=lower('" . $_SESSION["email"] . "');";
$pgclient->exeq($q);

$pgclient->disconnect();

echo "<div class='ok'>Contrase&ntilde;a actualizada con &eacute;xito</div>";

?>

