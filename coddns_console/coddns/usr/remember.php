<?php

include_once (dirname(__FILE__) . "/../lib/config.php");

if (! defined("_VALID_ACCESS")) {
    header ("Location: " . $config["html_root"] . "/");
    exit (1);
}


if (isset ($_SESSION["email"])) {
    header ("Location: /");
    exit (1);
}
if( !isset($_SESSION["lan"]) ){
    session_write_close();
    header ("Location: /?lang=es");
    exit (1);
}

$lan = $_SESSION["lan"];
session_write_close();
/* CASTELLANO */
$text["es"]["message"]="
    <p>Si has olvidado tu contrase&ntilde;a, introduce tu direcci&oacute;n de correo a continuaci&oacute;n.</p>
    <p>Se te enviar&aacute; un c&oacute;digo con un enlace para que modifiques tu contrase&ntilde;a</p>
";
$text["es"]["submit"] ="enviar";
$text["es"]["mail"]   ="correo electr&oacute;nico";
$text["es"]["title"]  ="Contrase&ntilde;a olvidada";

/* ENGLISH */
$text["en"]["message"]="
    <p>If you forgot your password, fill the form with your email</p>
    <p>We'll send you an email with a link to change your password to a new one</p>
";
$text["en"]["submit"] ="Submit";
$text["en"]["mail"]   ="email";
$text["en"]["title"]  ="Forgotten password";

/* DEUTCH */

?>
<section>
    <h1><?php echo $text[$lan]["title"];?></h1>
    <br>
    <?php echo $text[$lan]["message"]; ?>
    <form id="remember" onsubmit="fsgo('remember', 'response', '<?php echo $config["html_root"]; ?>/usr/sendtoken.php');return false;">
    <ul>
        <li>
            <input style="float:none;" type="email" name="u" required placeholder="<?php echo $text[$lan]["mail"];?>"/>
            <input style="float:none;" type="submit" value="<?php echo $text[$lan]["submit"];?>"/>
        </li>
    </ul>
    </form>
    <div id="response"></div>

</section>
