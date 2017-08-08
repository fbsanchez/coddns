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

defined("_VALID_ACCESS") or define ("_VALID_ACCESS", 1);


if (!file_exists(__DIR__ . "/include/config.php")){
    header("Location: install.php");
    exit(0);
}

require_once(__DIR__ . "/include/config.php");
require_once(__DIR__ . "/include/functions_ip.php");
require_once(__DIR__ . "/include/functions_util.php");
require_once(__DIR__ . "/lib/coduser.php");

/**
 * Language selector
 *
 */
$en   = array();
$es   = array();
$de   = array();
$text = array("es"=>$es,"en"=>$en,"de"=>$de);

session_start();

if(isset($_GET["lang"])){
    switch ($_GET["lang"]){
        case "es": 
        case "en": 
        case "de": 
            $_SESSION["lan"] = $_GET["lang"];
            break;
        default: $_SESSION["lan"]   = "en";
    }
}
else{
    if(! isset($_SESSION["lan"]) )
        $_SESSION["lan"] = "en";
}

/* shorter var name... */
$lan = $_SESSION["lan"];

session_write_close();

//disable some errors which aren't really errors:
ini_set('session.use_cookies',false);
session_cache_limiter(false);


/* CASTELLANO */
$text["es"]["welcome"]   ="Bienvenido";
$text["es"]["yourip"]    ="Tu direcci&oacute;n IP p&uacute;blica es: ";
$text["es"]["start"]     ="Inicio";
$text["es"]["back"]      = "Volver";
$text["es"]["downloads"]       ="Descargas";
$text["es"]["footer_title"]    ="Contactar/Pol&iacute;ticas";
$text["es"]["cookie_policy"]   ="Pol&iacute;tica de cookies";
$text["es"]["terms"]           ="Condiciones de uso";
$text["es"]["downloads_title"] ="Descargas disponibles del actualizador IP";
$text["es"]["nav_account"]     ="Mi cuenta";
$text["es"]["nav_logout"]      ="Desconectarme";

/* ENGLISH */
$text["en"]["welcome"]   ="Welcome";
$text["en"]["yourip"]    ="Your public IP address is: ";
$text["en"]["start"]     ="Home";
$text["en"]["back"]      = "Go back";
$text["en"]["downloads"]       ="Downloads";
$text["en"]["footer_title"]    ="Contact/Terms";
$text["en"]["cookie_policy"]   ="Cookie policy";
$text["en"]["terms"]           ="Terms of service";
$text["en"]["d_linux_header"]  ="You can download the dynamic DNS updater client for Linux from ";
$text["en"]["d_linux_link"]    ="here";
$text["en"]["nav_account"]     ="My account";
$text["en"]["nav_logout"]      ="Logout";

/* DEUTSCH */
$text["de"]["welcome"]   ="Willkommen";
$text["de"]["yourip"]    ="Ihre &ouml;ffentliche IP-Adresse ist: ";
$text["de"]["start"]     ="Heim";
$text["de"]["back"]      = "Zur&ouml;ck";
$text["de"]["downloads"]       ="Downloads";
$text["de"]["footer_title"]    ="Kontakt/Nutzungsbedingungen";
$text["de"]["cookie_policy"]   ="Cookie-Politik";
$text["de"]["terms"]           ="Nutzungsbedingungen";
$text["de"]["d_linux_header"]  ="Sie k&ouml;nnen die dynamischen DNS-Updater Client f&uuml;r Linux von ";
$text["de"]["d_linux_link"]    ="hier";
$text["de"]["nav_account"]     ="My account";
$text["de"]["nav_logout"]      ="Logout";


?>

<!DOCTYPE html>
<html lang="<?php echo $lan;?>">
<head>
<title>Custom Open Dynamic DNS</title>

<meta property="og:title" content="CODDNS" />
<meta property="og:description" content="Your IP: <?php echo _ip();?><br/> Dynamic DNS for everyone!" />
<meta property="og:image" content="/rs/img/ms-icon-310x310.png" />
<meta name="description" content="Your IP is <?php echo _ip();?>" />

<meta charset="UTF-8"/>
<meta name="google-site-verification" content="hBYboxJ02VZp_fkufkIvtjbyv-T98x6lnk4NBAROCpY" />
<link rel="apple-touch-icon" sizes="57x57" href="<?php echo $config["html_root"]; ?>/rs/img/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="<?php echo $config["html_root"]; ?>/rs/img/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="<?php echo $config["html_root"]; ?>/rs/img/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="<?php echo $config["html_root"]; ?>/rs/img/apple-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="<?php echo $config["html_root"]; ?>/rs/img/apple-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="<?php echo $config["html_root"]; ?>/rs/img/apple-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="<?php echo $config["html_root"]; ?>/rs/img/apple-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="<?php echo $config["html_root"]; ?>/rs/img/apple-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo $config["html_root"]; ?>/rs/img/apple-icon-180x180.png">
<link rel="icon" type="image/png" sizes="192x192"  href="<?php echo $config["html_root"]; ?>/rs/img/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo $config["html_root"]; ?>/rs/img/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="<?php echo $config["html_root"]; ?>/rs/img/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo $config["html_root"]; ?>/rs/img/favicon-16x16.png">
<link rel="manifest" href="<?php echo $config["html_root"]; ?>/rs/img/manifest.json">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="<?php echo $config["html_root"]; ?>/rs/img/ms-icon-144x144.png">
<meta name="theme-color" content="#ffffff">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo $config["html_root"]; ?>/rs/img/favicon.ico">


<link rel="stylesheet" type="text/css" href="<?php echo $config["html_root"] . "/rs/css/" . $config["html_view"]; ?>/main.css">
<link rel="stylesheet" type="text/css" href="<?php echo $config["html_root"] . "/rs/css/" . $config["html_view"]; ?>/header.css">

<script type="text/javascript" src="<?php echo $config["html_root"]; ?>/rs/js/util.js"></script>
<script type="text/javascript">
    function checkHostName(){
        updateContent("rec_info", "rest_host.php", "h="+document.getElementById('h').value);
        return false;
    }
    function select_my_ip(){
        ip.value="<?php echo _ip();?>";
        return false;
    }
</script>

</head>

<body>

<?php

/**
 * How to access resources:
 *
 * m  = folder
 * z  = file
 * op = file tail
 *
 * In example: coddns.org/index.php?m=usr&z=hosts&op=mod
 * Results in file include: usr/hosts/hosts_mod.php
 *
 */



// Restrict acces:
//   adm: default auth_level: 100
//   usr: default auth_level: 1


$mode = secure_get("m");
$zone = secure_get("z");
$operation = secure_get("op");
$url  = "";

if (isset ($mode)){
    $url = $mode . DIRECTORY_SEPARATOR;
}
if (! isset ($zone)){
    if (!isset ($mode)){ // avoid recursive inclusion on clean call
        $url .= "main.php";
    }
    else {
        $url .= "index.php";
    }
}
elseif (! isset($operation)){
    $url .= $zone . "/" . $zone . ".php";
}
else {
    $url .= $zone . DIRECTORY_SEPARATOR . $zone . "_" . $operation . ".php";
}

include_once("header.php");

$auth_level_required = get_required_auth_level($mode,$zone,$operation);

?>
<div id="main">
<?php 


try {
    $user = new CODUser();
    $user->check_auth_level($auth_level_required);
}
catch (Exception $e) {
    echo $e->getMessage();
    exit (1);
}


if (isset($_GET["debug_mode"]) && ($_GET["debug_mode"] == 1)) {
    ?>
<div style="width: 200px; height: 200px; position: fixed; top:0; right:0;border:1px solid red;">
<?php
echo "Auth_level: " . $user->get_auth_level();
echo "</div>";
}
?>

<?php
if (! file_exists(__DIR__ . DIRECTORY_SEPARATOR . $url)) {
    include (__DIR__ . DIRECTORY_SEPARATOR . "err404.php");
}
else {
    if ($auth_level_required === null){
        include (__DIR__ . DIRECTORY_SEPARATOR . "err502.html");
    }
    else {
        include (__DIR__ . DIRECTORY_SEPARATOR . $url);
    }
}
?>

<div id="ajax_container">
<script type="text/javascript">
document.onkeyup = function(evt) {
    evt = evt || window.event;
    if (evt.keyCode == 27) {
        close_ajax_message();
    }
};
</script>
<div id ="ajax_message_wrapper">
    <b><a title="dismiss" href="#" id ="ajax_message_close" onclick="close_ajax_message();">x</a></b>
    <div id="ajax_message" onchange="raise_ajax_message();"></div>
    </div>
</div>
</div>
</body>

</html>


<?php
    $config["dbh"]->disconnect();
?>