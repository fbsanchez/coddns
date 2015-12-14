<?php
include_once ("include/config.php");
require_once ("lib/ipv4.php");
require_once ("lib/responsive.php");

if( !isset($_SESSION["lan"])){
    session_write_close();
    header ("Location: " . $config["html_root"] . "?lang=es");
    exit (1);
}

/* CASTELLANO */
$text["es"]["main_welcome"]="
    <h2>&iexcl;Hola!</h2>
    <p>&iquest;Necesitas acceso a tu <b>servidor privado</b> de casa <b>desde internet</b>? &iquest;o quieres ver las c&aacute;maras de vigilancia de tu domicilio? Es posible que prefieras <b>acceder a tus contenidos multimedia desde cualquier sitio</b> sin tener que cargar con un disco duro extra&iacute;ble o contratar (y pagar...) una IP est&aacute;tica, o tener que confirmar tu direcci&oacute;n de correo cada mes para mantener el servicio...</p>
    <br>
    <p><b>&iexcl;Para eso estamos aqu&iacute;!</b></p>
    <br>
    <p>Con CODDNS tendr&aacute;s siempre una etiqueta a trav&eacute;s de la cual <b>podr&aacute;s acceder a la red de tu casa</b>, sin tener que estar preocupandote de los cambios en la IP del router.</p>
    <p>Simplemente asocia una etiqueta disponible a tu direcci&oacute;n IP, instala el actualizador y accede a tu equipo desde cualquier parte de Internet.</p>
    <br>
    <p><b>Bienvenidos</b></p>
    <br>
    <hr>
";

/* ENGLISH */
$text["en"]["main_welcome"]="
    <h2>Hi!</h2>
    <p>Need access to your <b>private server</b> at home <b>from Internet?</b> Or do you want to see the surveillance cameras in your home? You may prefer to <b>access your personal media from anywhere</b> without having to carry a removable hard drive or hire (and paid...) a static IP, or have to confirm your email every month to keep the service ...</p>
    <br>
    <p><b>That's why we're here!</b></p>
    <br>
    <p>With CODDNS you will always have a label through which you'll be able to <b>access your home network</b> without having to worrying about changes in the public IP address given for router from your ISP.</p>
    <p>Simply associates a label available your public IP address, install the IP updater and access your computer from anywhere on the Internet.</p>
    <br>
    <p><b>Welcome</b></p>
    <br>
    <hr>
";

/* DEUTSCH */
$text["de"]["main_welcome"]="
    <h2>Hallo!</h2>
    <p>Ben&ouml;tigen den Zugriff auf Ihre privaten Server zu Hause? Oder wollen Sie, um die &uuml;berwachungskameras in Ihrem Haus sehen? Sie k&ouml;nnen es vorziehen, Ihre Medien von &uuml;berall aus zugreifen, ohne eine Wechselfestplatte durchf&uuml;hren oder mieten Sie eine statische IP, oder m&uuml;ssen Sie Ihre E-Mail best&auml;tigen jeden Monat, um den Dienst zu halten...</p>
    <br>
    <p><b>Das ist, was wir hier sind!</b></p>
    <br>
    <p>Sie k&ouml;nnen dieses Tool verwenden, um immer ein Etikett, &uuml;ber die mit Ihrem Heimnetzwerk, ohne sich Gedanken &uuml;ber &auml;nderungen der IP-Router zugreifen.</p>
    <p>Einfach verbindet ein Label verf&uuml;gbar Ihre IP-Adresse, installieren Sie den Updater und auf Ihrem Computer von &uuml;berall &uuml;ber das Internet.</p>
    <br>
    <p><b>Herzlich Willkommen</b></p>
    <br>
    <hr>
";
$text["de"]["main_reg"]    = "Sich eintragen";
$text["de"]["main_acc"]    = "Einloggen";
$text["de"]["ph_email"]    = "email";
$text["de"]["ph_pass"]     = "password";
$text["en"]["ph_cpass"]    = "confirm password";
$text["de"]["f_send"]      = "Send";
$text["de"]["label_cpass"] = "Confirm password:";
$text["de"]["remember"]    = "Did you forgot your password?";

?>

<section style="margin-bottom: 20px; text-align: justify;">
<article>
    <?php echo $text[$lan]["main_welcome"];?>
</article>
</section>

