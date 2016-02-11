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

if (! defined("_VALID_ACCESS")) { // Avoid direct access
    header ("Location: " . $config["html_root"] . "/");
    exit (1);
}

$auth_level_required = get_required_auth_level('usr','users','mod');
$user = new CODUser();
$user->check_auth_level($auth_level_required);

session_start();
if (!isset($_SESSION["lan"])){
    $_SESSION["lan"] = "es";
}
$lan = $_SESSION["lan"];
session_write_close();


/* CASTELLANO */
$text["es"]["ua_welcome"]    = "
    <h2>Mi cuenta</h2>
    <p>Desde aqu&iacute; puedes administrar tus datos de acceso.</p>
";
$text["es"]["ua_label_mail"] = "Direcci&oacute;n de correo:";
$text["es"]["ua_label_pass_old"]   = "Contrase&ntilde;a antigua:";
$text["es"]["ua_label_pass_new"]   = "Contrase&ntilde;a nueva:";
$text["es"]["ua_label_pass_check"] = "Confirmar contrase&ntilde;a:";
$text["es"]["ua_place_pass_old"]   = "antigua";
$text["es"]["ua_place_pass_new"]   = "nueva";
$text["es"]["ua_place_pass_check"] = "confirma";
$text["es"]["pass_updated_ok"]     = "Contrase&ntilde;a actualizada";
$text["es"]["send"] = "Cambiar";

/* ENGLISH */
$text["en"]["ua_welcome"] = "
    <h2>My account</h2>
    <p>You can manage your access data from here.</p>
";
$text["en"]["ua_label_mail"] = "E-mail:";
$text["en"]["ua_label_pass_old"]   = "Old password:";
$text["en"]["ua_label_pass_new"]   = "New password:";
$text["en"]["ua_label_pass_check"] = "Re-type password:";
$text["en"]["ua_place_pass_old"]   = "old";
$text["en"]["ua_place_pass_new"]   = "new";
$text["en"]["ua_place_pass_check"] = "confirm";
$text["en"]["pass_updated_ok"]     = "Password successfully updated";
$text["en"]["send"] = "Change it";

/* DEUTSCH */
$text["de"]["ua_welcome"] = "
    <h2>My account</h2>
    <p>You can manage your access data from here.</p>
";
$text["de"]["ua_label_mail"] = "E-mail:";
$text["de"]["ua_label_pass_old"]   = "Old password:";
$text["de"]["ua_label_pass_new"]   = "New password:";
$text["de"]["ua_label_pass_check"] = "Re-type password:";
$text["de"]["ua_place_pass_old"]   = "old";
$text["de"]["ua_place_pass_new"]   = "new";
$text["de"]["ua_place_pass_check"] = "confirm";
$text["de"]["pass_updated_ok"]     = "Password successfully updated";
$text["de"]["send"] = "Change it";

?>
<!DOCTYPE html>


<html>

<head>
</head>

<body>

<section>
<?php echo $text[$lan]["ua_welcome"];?>
    <p><?php echo $text[$lan]["ua_label_mail"];?> <span style="font-style:italic;"><?php echo $_SESSION["email"];?></span>
    </p>
    <form name="npass" method="POST" onsubmit="fsgo('npass','response','usr/users_rq_mod.php');return false">
    <ul>
        <li>
            <label><?php echo $text[$lan]["ua_label_pass_old"];?></label><input type="password" id="op" name="op" placeholder="<?php echo $text[$lan]["ua_place_pass_old"];?>" autofocus required />
        </li>
        <li>
            <label><?php echo $text[$lan]["ua_label_pass_new"];?></label><input type="password" id="np" name="np" placeholder="<?php echo $text[$lan]["ua_place_pass_new"];?>" required />
        </li>
        <li>
            <label><?php echo $text[$lan]["ua_label_pass_check"];?></label><input type="password" id="cp" name="cp" placeholder="<?php echo $text[$lan]["ua_place_pass_check"];?>" required />
        </li>
        <li>
            <input type="submit" value="<?php echo $text[$lan]["send"];?>"/>
        </li>
    </ul>
    <div id="response"></div>
    </form>
</section>
</body>
