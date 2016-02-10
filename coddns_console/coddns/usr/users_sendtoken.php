<?php
require_once (dirname(__FILE__) . "/../include/config.php");
require_once (dirname(__FILE__) . "/../lib/db.php");
require_once (dirname(__FILE__) . "/../lib/ipv4.php");

if (! defined("_VALID_ACCESS")) {
    header ("Location: " . $config["html_root"] . "/");
    exit (1);
}

session_start();

/* CASTELLANO */
$text["es"]["ok"]   = "<div class='ok'>Se ha enviado un email a " . $_POST["u"] . " con las instrucciones para resetear su contrase&ntilde;a.</div>";
$text["es"]["err1"] = "<div class='err'>Rellene todos los datos</div>";
$text["es"]["err2"] = "<div class='err'>No cumple las longitudes minimas</div>";
$text["es"]["err3"] = "<div class='err'>No hay users con los datos provistos, prueba a crear uno nuevo.</div>";
$text["es"]["dberror"] = "<div class='err'>Woooops, contacte con el administrador del sitio.</div>";


/* ENGLISH */
$text["en"]["ok"]   = "<div class='ok'>An email have been sent to " . $_POST["u"] . " with the instructions to reset the password.</div>";
$text["en"]["err1"] = "<div class='err'>Please fill all data</div>";
$text["en"]["err2"] = "<div class='err'>The data provided is not reaching the minimal length";
$text["en"]["err3"] = "<div class='err'>There's no user with the providen data, try to register a new one.</div>";
$text["en"]["dberror"] = "<div class='err'>Woooops, we have a problem! please contact the site administrator.</div>";


if( !isset($_SESSION["lan"])){
    session_write_close();
    header ("Location: /?lang=es");
    exit (1);
}

$lan = $_SESSION["lan"];

session_write_close();


if ( ! isset($_POST["u"]) ){
    echo $text[$lan]["err1"];
    exit(1);
}


if ( strlen($_POST["u"]) < MIN_USER_LENGTH){
    echo $text[$lan]["err2"];
    exit(2);
}


$salt="as!09**31sfSAFasfaNYGFB";

$dbclient = new DBClient($db_config);
$strenght = 4;
$user = $dbclient->prepare($_POST["u"], "email");
$hash = hash ("sha256",$salt . openssl_random_pseudo_bytes($strenght) . rand());

$dbclient->connect() or die ($text[$lan]["dberror"]);

$q = "Select * from users where lower(mail)=lower('" . $user . "');";
$r = $dbclient->fetch_object ($dbclient->exeq($q));
if ($dbclient->lq_nresults() == 0){ // USER NON EXISTENT OR PASSWORD ERROR
    echo $text[$lan]["err3"];
    exit (3);
}

/* ----------------------------- */
/* CASTELLANO */
$text["es"]["subject"] = "Recuperar acceso a CODDNS";
$text["es"]["mailbody"]= "
<h3>Hola!</h3>
<p>Hemos recibido una solicitud de cambio de contrase&ntilde;a desde " . _ip() . "</p>
<p>Si no has iniciado ninguna acci&oacute;n no es necesario que hagas nada.</p>
<p>En caso de que realmente quieras cambiar tus datos de acceso, por favor, sigue el siguiente enlace:</p>
<a href='http://" . $config["domainname"] . "/?z=newpassword&token=" . $hash . "'>Cambiar mi contrase&ntilde;a</a>
<p> Si el enlace no funciona copia el siguiente texto en el navegador para acceder.</p>
http://" . $config["domainname"] . "/?z=newpassword&token=" . $hash . "
<p>Gracias!</p>
<p>Saludos,</p>
<p>CODDNS</p>
";


/* ENGLISH */
$text["en"]["subject"] = "Recover access to CODDNS";
$text["en"]["mailbody"]= "
<h3>Hi!</h3>
<p>We'd received a request to change your password from " . _ip() . "</p>
<p>if you have not initiated any action need not do anything.</p>
<p>If you really want to change your password, please follow next link:</p>
<a href='http://" . $config["domainname"] . "/?z=newpassword&token=" . $hash . "'>Cambiar mi contrase&ntilde;a</a>
<p> If the link does not work, please copy, paste and go.</p>
http://" . $config["domainname"] . "/?z=newpassword&token=" . $hash . "
<p>Thank you!</p>
<p>Regards,</p>
<p>CODDNS</p>
";

/* ------------------------ */


/* User found! */

$text_sender               = "CODDNS";
$email_sender              = "noreply@" . $config["domainname"];
$text_mail_welcome_body    = $text[$lan]["mailbody"];
$text_mail_welcome_subject = $text[$lan]["subject"];

$q = "update users set hash='" . $hash . "', max_time_valid_hash = now()+Interval '30 minutes' where lower(mail)=lower('" . $user . "');";
$dbclient->exeq($q);

$dbclient->disconnect();


$recipient = $user;                    //recipient
$mail_body = $text_mail_welcome_body;  //mail body
$subject = $text_mail_welcome_subject; //subject
$header  = "From: " . $text_sender . " <" . $email_sender . ">\r\n";
//optional headerfields
$header .= "MIME-Version: 1.0\r\n";
$header .= "Content-type: text/html; charset=iso-8859-1\r\n";
mail($recipient, $subject, $mail_body, $header); //mail command :)


echo $text[$lan]["ok"];


?>

