<?php

if (   (isset ($_SESSION["email"]))
    || (! isset ($_GET["token"]))) {
    header ("Location: /");
    exit (1);
}

if (!isset ($_SESSION["email"])) {
    header ("Location: /");
    exit (1);
}
if( !isset($_SESSION["lan"]) ){
    $_SESSION["lan"]= "es";
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

<form name="changepass" method="POST" onsubmit="fsgo('changepass','response','/usr/rq_newpass.php');return false;">
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

