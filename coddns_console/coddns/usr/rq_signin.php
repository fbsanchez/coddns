<?php
require_once (dirname(__FILE__) . "/../include/config.php");
require_once (dirname(__FILE__) . "/../lib/db.php");
require_once (dirname(__FILE__) . "/../lib/ipv4.php");

session_start();

if( !isset($_SESSION["lan"]) ){
    session_write_close();
    header(" Location:" . $config["root_html"] . "/?lang=es");
    exit (1);
}

$lan = $_SESSION["lan"];

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

$text_sender               = "CODDNS";
$email_sender              = "noreply@" . $config["domainname"];;
$text_mail_welcome_body    = "Hola!\n\n Ya formas parte de los usuariuos de custom open dynamic DNS :D";
$text_mail_welcome_subject = "Gracias por registrarte!";


$dbclient = new DBClient($db_config);

$user = $dbclient->prepare($_POST["u"], "email");
$pass = hash ("sha512",$salt . $rq_pass);

$dbclient->connect() or die ($text[$lan]["dberror"]);

$q = "Select * from " . $db_config["schema"] . ".users where lower(mail)=lower('" . $user . "');";
$dbclient->exeq($q) or die ($text[$lan]["dberror"]);
if ($dbclient->lq_nresults() == 0){ // ADD NEW USER
    $q = "insert into " . $db_config["schema"] . ".users (mail,pass, ip_last_login, first_login) values (lower('" . $user . "'),'" . $pass . "', '" . _ip() . "', now());";
    $dbclient->exeq($q) or die ($text[$lan]["dberror"]);

    $recipient = $user;                    //recipient
    $mail_body = $text_mail_welcome_body;  //mail body
    $subject = $text_mail_welcome_subject; //subject
    $header = "From: " . $text_sender . " <" . $email_sender . ">\r\n"; //optional headerfields
    mail($recipient, $subject, $mail_body, $header); //mail command :)

}
else {
    die ("<div class='err'>Ese usuario ya existe</div>");
    exit(1);
}

$dbclient->disconnect();

$_SESSION["email"] = $user;
$_SESSION["time"]  = time();

session_write_close();

?>
<div class='ok'>Bienvenido <?php echo $user; ?></div><script>location.reload();</script></div>

