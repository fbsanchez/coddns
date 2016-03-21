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

require_once (dirname(__FILE__) . "/../include/config.php");
require_once (dirname(__FILE__) . "/../lib/db.php");
require_once (dirname(__FILE__) . "/../lib/util.php");
require_once (dirname(__FILE__) . "/../lib/coduser.php");

$auth_level_required = get_required_auth_level('usr','users','rq_signin');
$user = new CODUser();
$user->check_auth_level($auth_level_required);

session_start();
if (!isset($_SESSION["lan"])){
    $_SESSION["lan"] = "es";
}
$lan = $_SESSION["lan"];
session_write_close();

if ($user->get_is_logged()){
    redirect($config[html_root] . "/?z=usr&m=hosts");
}

/* CASTELLANO */
$text["es"]["dberror"] = "<div class='err'>Wooops, contacte con el administrador del sitio</div>";

/* ENGLISH */
$text["en"]["dberror"] = "<div class='err'>Woooops, we have a problem! please contact the site administrator.</div>";


/* DEUTSCH */

if ( (! isset($_POST["u"])) || (! isset($_POST["p"])) || (! isset($_POST["pp"])) ){
    echo "<div class='err'>Rellene todos los datos</div>";
    exit(1);
}

$rq_pass = base64_decode($_POST["p"]);

if ( ( strlen($_POST["u"]) < MIN_USER_LENGTH) || ( strlen($rq_pass) < MIN_PASS_LENGTH) ){
    echo "<div class='err'>No cumple las longitudes minimas</div>";
    exit(2);
}

if ($_POST["p"] != $_POST["pp"]){
    echo "<div class='err'>La confirmaci&oacute;n de contrase&ntilde;a no coincide</div>";
    exit(3);
}

$text_sender               = "CODDNS desde " . $config["domainname"];
$email_sender              = "noreply@" . $config["domainname"];
$text_mail_welcome_body    = "Hola!\n\n Ya formas parte de los usuarios de custom open dynamic DNS :D";
$text_mail_welcome_subject = "Gracias por registrarte!";

$user = $_POST["u"];

$objUser = new CODUser();
if ($objUser->signin($user, $rq_pass) == null ) {
    echo "<div class='err'>Ese usuario ya existe</div>";
    exit (3);
}

$recipient = $user;                    //recipient
$mail_body = $text_mail_welcome_body;  //mail body
$subject = $text_mail_welcome_subject; //subject
$header = "From: " . $text_sender . " <" . $email_sender . ">\r\n"; //optional headerfields
mail($recipient, $subject, $mail_body, $header); //mail command :)

?>
<div class='ok'>Bienvenido <?php echo $user; ?></div><script>location="<?php echo $config["html_root"];?>/?m=usr&z=hosts";</script></div>
