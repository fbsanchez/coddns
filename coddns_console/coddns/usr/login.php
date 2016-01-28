<?php

include_once(dirname(__FILE__) . "/../include/config.php");

if (! defined("_VALID_ACCESS")) {
    header ("Location: " . $config["html_root"] . "/?lang=es");
    exit (1);
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
    <form id="loginf" method="POST" action="usr/rq_login.php" onsubmit="fsgo('loginf', 'login_response','usr/rq_login.php', true);return false;">
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
            <a id="remember" href="<?php echo $config["html_root"];?>/?z=remember&lang=<?php echo $lan;?>"><?php echo $text[$lan]["remember"];?> </a>
            <input type="submit" value="<?php
                echo $text[$lan]["f_send"];
            ?>"/>
        </li>
    </ul>
    <div id="login_response"></div>
    </form>
</section>

<section id="signin">
    <h2><?php echo $text[$lan]["main_reg"];?></h2>
    <form id="signinf" method="POST" action="usr/rq_signin.php" onsubmit="fsgo('signinf', 'signin_response','usr/rq_signin.php', true);return false;">
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


