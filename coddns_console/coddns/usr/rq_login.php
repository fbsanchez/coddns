<?php
require_once (dirname(__FILE__) . "/../include/config.php");
require_once (dirname(__FILE__) . "/../lib/coduser.php");

session_start();

if (!isset($_SESSION["lan"])){
	$_SESSION["lan"] = "es";
}
$lan = $_SESSION["lan"];

session_write_close();
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

$objUser = new CODUser();
if ($objUser->login($_POST["u"], $rq_pass) == null ) {
	echo $text[$lan]["err3"];
	exit (3);
}

echo $text[$lan]["welcome"];
//header ("Location: /?lang=" . $lan . "&z=hosts");
?>

