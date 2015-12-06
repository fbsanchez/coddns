<?php

require_once ("../include/config.php");

if (! isset ($_SESSION["email"]) ){
	header ("Location: " . $config["html_root"]);
    exit(1);
}

if( !isset($_SESSION["lan"]) ){
    session_write_close();
    header ("Location: " . $config["html_root"] . "/?lang=es");
    exit (1);
}

$lan = $_SESSION["lan"];

/* CASTELLANO */
$text["es"]["ua_welcome"]    = "
    <h1>Mi cuenta</h1>
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
    <h1>My account</h1>
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
    <h1>My account</h1>
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
    <form name="npass" method="POST" onsubmit="fsgo('npass','response','usr/rq_ua.php');return false">
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
    <a href="<?php echo $config["html_root"];?>/?lang=<?php echo $lan;?>"><?php echo $text[$lan]["back"];?></a>
</section>
</body>
