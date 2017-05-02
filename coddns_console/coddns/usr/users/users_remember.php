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

require_once (__DIR__ . "/../../include/config.php");
require_once (__DIR__ . "/../../lib/db.php");
require_once (__DIR__ . "/../../include/functions_util.php");
require_once (__DIR__ . "/../../lib/coduser.php");

if (! defined("_VALID_ACCESS")) { // Avoid direct access
    header ("Location: " . $config["html_root"] . "/");
    exit (1);
}

try {
    $auth_level_required = get_required_auth_level('usr','users','remember');
    $user = new CODUser();
    $user->check_auth_level($auth_level_required);
}
catch (Exception $e) {
    echo $e->getMessage();
    exit (1);
}

session_start();
if (!isset($_SESSION["lan"])){
    $_SESSION["lan"] = "es";
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
    <form id="remember" onsubmit="fsgo('remember', 'response', '<?php echo $config["html_root"]; ?>/usr/users/users_sendtoken.php');return false;">
    <ul>
        <li>
            <input style="float:none;" type="email" name="u" required placeholder="<?php echo $text[$lan]["mail"];?>"/>
            <input style="float:none;" type="submit" value="<?php echo $text[$lan]["submit"];?>"/>
        </li>
    </ul>
    </form>
    <div id="response"></div>

</section>
