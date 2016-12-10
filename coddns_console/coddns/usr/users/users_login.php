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
require_once (__DIR__ . "/../../lib/util.php");
require_once (__DIR__ . "/../../lib/coduser.php");

if (! defined("_VALID_ACCESS")) { // Avoid direct access
    header ("Location: " . $config["html_root"] . "/");
    exit (1);
}

$auth_level_required = get_required_auth_level('usr','users','login');
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
$text["es"]["main_reg"]    = "Registrarme";
$text["es"]["main_acc"]    = "Acceder";
$text["es"]["ph_email"]    = "correo electr&oacute;nico";
$text["es"]["ph_pass"]     = "password";
$text["es"]["ph_cpass"]    = "confirma password";
$text["es"]["f_send"]      = "Enviar";
$text["es"]["label_cpass"] = "Confirma password:";
$text["es"]["remember"]    = "&iquest;Olvid&oacute; su contrase&ntilde;a?";

/* ENGLISH */
$text["en"]["main_reg"]    = "Sign in";
$text["en"]["main_acc"]    = "Log in";
$text["en"]["ph_email"]    = "email";
$text["en"]["ph_pass"]     = "password";
$text["en"]["ph_cpass"]    = "confirm password";
$text["en"]["f_send"]      = "Send";
$text["en"]["label_cpass"] = "Confirm password:";
$text["en"]["remember"]    = "Did you forgot your password?";


?>


<section id="login">
    <h2><?php echo $text[$lan]["main_acc"];?></h2>
    <form id="loginf" method="POST" action="#" onsubmit="fsgo('loginf', 'login_response','usr/users/users_rq_login.php', true);return false;">
    <ul>
        <li>
            <label>E-mail: </label>
            <input type="email" name="u" placeholder="<?php
                echo $text[$lan]["ph_email"];
            ?>" required />
        </li>
        <li>
            <label>Password: </label>
            <input type="password" name="p" id="p" placeholder="password" required/>
        </li>
        <li>
            <a id="remember" href="<?php echo $config["html_root"];?>/?m=usr&z=users&op=remember&lang=<?php echo $lan;?>"><?php echo $text[$lan]["remember"];?> </a>
            <input type="submit" value="<?php
                echo $text[$lan]["f_send"];
            ?>"/>
        </li>
    </ul>
    <div id="login_response"></div>
    </form>
</section>

<?php
if (get_required_auth_level('usr','users','signin') == 0){
    // SHOW THE SIGNIN FORM IF IT'S ENABLED ON CONFIGURATION
?>
<section id="signin">
    <h2><?php echo $text[$lan]["main_reg"];?></h2>
    <form id="signinf" method="POST" action="#" onsubmit="fsgo('signinf', 'signin_response','usr/users/users_rq_signin.php', true);return false;">
    <ul>
        <li>
            <label>E-mail: </label>
            <input type="email" name="u" placeholder="<?php
                echo $text[$lan]["ph_email"];
            ?>" required/>
        </li>
        <li>
            <label>Password: </label>
            <input type="password" name="p" id="sp" placeholder="password" required/>
        </li>
        <li>
            <label><?php echo $text[$lan]["label_cpass"]; ?></label>
            <input type="password" name="pp" id="spp" placeholder="<?php
                echo $text[$lan]["ph_cpass"];
            ?>" required/>
        </li>
            <li>
            <input type="submit" value="<?php
                echo $text[$lan]["f_send"];
            ?>"/>
        </li>
    </ul>

    <div id="signin_response"></div>
    </form>
</section>
<?php
}
else {
?>
<section>
    <article style="margin-top: 50px;">
        <p>&gt; El registro no est&aacute; disponible, por favor, p&oacute;ngase en contacto con el administrador del sistema para recibir sus credenciales.</p>
    </article>
</section>
<?php
}
?>

