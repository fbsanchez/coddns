<?php
require_once (dirname(__FILE__) . "/../include/config.php");
require_once (dirname(__FILE__) . "/../lib/ipv4.php");
require_once (dirname(__FILE__) . "/../lib/db.php");

//check_user_auth();

session_start();
if (!isset($_SESSION["lan"])){
    $_SESSION["lan"] = "es";
}
$lan = $_SESSION["lan"];
session_write_close();


/* CASTELLANO */
$text["es"]["err1"] = "<div class='err'>Es necesario que facilite todos los datos</div>";
$text["es"]["err2"] = "<div class='err'>No cumple las longitudes minimas</div>";
$text["es"]["err3"] = "<div class='err'>La confirmaci&oacute;n de contrase&ntilde;a no coincide</div>";
$text["es"]["err4"] = "<div class='err'>El token no es v&aacute;lido o ha caducado</div>";
$text["es"]["ok"]   = "<div class='ok'>Su contrase&ntilde;a se ha cambiado correctamente</div><a href='/?lang=$lan'>Ir a la p&aacute;gina principal</a>";
$text["es"]["dberror"] = "<div class='err'>Woooops, contacte con el administrador del sitio.</div>";

/* ENGLISH */
$text["en"]["err1"] = "<div class='err'>You need to provide all data.</div>";
$text["en"]["err2"] = "<div class='err'>Data provided doesn't reach minimal lenght</div>";
$text["en"]["err3"] = "<div class='err'>The password check doesn't match the given password.</div>";
$text["en"]["err4"] = "<div class='err'>The token provided is not valid</div>";
$text["en"]["ok"]   = "<div class='ok'>Your password have been correctly changed</div><a href='/?lang=$lan'>Go to main page</a>";
$text["en"]["dberror"] = "<div class='err'>Woooops, we have a problem! please contact the site administrator.</div>";

/* DEUTSCH */

if (   (! isset($_POST["u"]))
    || (! isset($_POST["p"]))
    || (! isset($_POST["cp"]))
    || (! isset($_POST["t"])) ){
    echo $text[$lan]["err1"];
    exit(1);
}

$rq_npass = (base64_decode($_POST["p"]));
$rq_cpass = (base64_decode($_POST["cp"]));

if (   ( strlen($_POST["u"]) < MIN_USER_LENGTH)
    || ( strlen($rq_npass) < MIN_PASS_LENGTH)
    || ( strlen($rq_cpass) < MIN_PASS_LENGTH)){
    echo $text[$lan]["err2"];
    exit(2);
}

if ($_POST["p"] != $_POST["cp"]){
    echo $text[$lan]["err3"];
    exit(3);
}

$dbclient = new DBClient($db_config);

$user  = $dbclient->prepare($_POST["u"], "email");
$pass  = hash ("sha512",$salt . $rq_npass);
$token = $dbclient->prepare($_POST["t"], "text");

$dbclient->connect() or die ($text[$lan]["dberror"]);

$q = "Select * from users where lower(mail)=lower('" . $user . "') and hash='" . $token . "' and now() < max_time_valid_hash;";
$dbclient->exeq($q);
if ($dbclient->lq_nresults() == 0){ // No results, no valid hash
    echo $text[$lan]["err4"];
    exit (4);
}

$q = "update users set pass='" . $pass . "' where lower(mail)=lower('" . $user . "');";
$dbclient->exeq($q);
$q = "update users set hash='' where lower(mail)=lower('" . $user . "');";
$dbclient->exeq($q);
$q = "update users set max_time_valid_hash=null where lower(mail)=lower('" . $user . "');";
$dbclient->exeq($q);

$dbclient->disconnect();


echo $text[$lan]["ok"];

?>
