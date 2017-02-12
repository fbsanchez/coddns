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

$auth_level_required = get_required_auth_level('usr','users','resetpass');
$user = new CODUser();
$user->check_auth_level($auth_level_required);

session_start();
if (!isset($_SESSION["lan"])){
    $_SESSION["lan"] = "es";
}
$lan = $_SESSION["lan"];
session_write_close();



/* CASTELLANO */
$text["es"]["title"]    = "Cambiemos la contrase&ntilde;a";
$text["es"]["message"]  = "Hola, introduce la nueva contrase&ntilde;a a continuaci&oacute;n";
$text["es"]["mail"]     = "correo electr&oacute;nico";
$text["es"]["submit"]   = "enviar";
$text["es"]["ph_pass"]  = "nueva contrase&ntilde;a";
$text["es"]["ph_cpass"] = "confirma contrase&ntilde;a";

/* ENGLISH */
$text["en"]["title"]    = "Let's change the password";
$text["en"]["message"]  = "Hi, set your new password in the inputs below.";
$text["en"]["submit"]   = "Submit";
$text["en"]["mail"]     = "email";
$text["en"]["ph_pass"]  = "new password";
$text["en"]["ph_cpass"] = "confirm password";


?>

<section>
<h1><?php echo $text[$lan]["title"];?></h1>
<br>
<p><?php echo $text[$lan]["message"];?></p>

<form name="changepass" method="POST" onsubmit="fsgo('changepass','response','<?php echo $config["html_root"];?>/usr/users/users_rq_resetpass.php');return false;">
    <input name="t" style="float: none;color:#888;text-indent:0;" type="hidden" value="<?php echo $_GET["token"];?>" readonly required/>
    <ul>
        <li style="text-align: center;">
            <input name="u" style="float: none;" type="email" placeholder="<?php echo $text[$lan]["mail"];?>" required/>
        </li>
        <li style="text-align: center;">
            <input name="p" style="float: none;" type="password" placeholder="<?php echo $text[$lan]["ph_pass"];?>" required/>
        </li>
        <li style="text-align: center;">
            <input name="cp" style="float: none;" type="password" placeholder="<?php echo $text[$lan]["ph_cpass"];?>" required/>
        </li>
        <li style="text-align: center;">
            <input style="float: none;" type="submit" value="<?php echo $text[$lan]["submit"];?>"/>
        </li>
    </ul>
    <div id="response"></div>
</form>

</section>

