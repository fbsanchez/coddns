<?php

defined("_VALID_ACCESS") or define ("_VALID_ACCESS", 1);

session_start();
include_once (dirname(__FILE__) . "/include/config.php");
require_once (dirname(__FILE__) . "/lib/ipv4.php");
require_once (dirname(__FILE__) . "/lib/util.php");

if (!file_exists(dirname(__FILE__) . "/include/config.php")){
    header("Location: install.php");
}

if (get_user_auth() && (isset($_GET["z"])) && ($_GET["z"] == "login")){
    $_GET["z"] = "hosts";
}

/**
 * Language selector
 *
 */
$en   = array();
$es   = array();
$de   = array();
$text = array("es"=>$es,"en"=>$en,"de"=>$de);

if(isset($_GET["lang"])){
    switch ($_GET["lang"]){
        case "es": 
        case "en": 
        case "de": 
            $_SESSION["lan"] = $_GET["lang"];
            break;
        default: $_SESSION["lan"]   = "es";
    }
}
else{
    if(! isset($_SESSION["lan"]) )
        $_SESSION["lan"] = "es";
}

/* shorter var name... */
$lan = $_SESSION["lan"];

session_write_close();

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
<meta charset="UTF-8"/>
<meta name="google-site-verification" content="hBYboxJ02VZp_fkufkIvtjbyv-T98x6lnk4NBAROCpY" />
<link rel="icon" href="rs/img/coddns.ico">

<?php
    if(isOverHTTPS()) {
?>
<link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
<?php
} else {
?>
<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
<?php
}

if (! check_user_agent('mobile') ){
?>
<link rel="stylesheet" type="text/css" href="rs/css/pc/main.css">
<link rel="stylesheet" type="text/css" href="rs/css/pc/header.css">
<?php
} else {
?>
<link rel="stylesheet" type="text/css" href="rs/css/m/main.css">
<link rel="stylesheet" type="text/css" href="rs/css/m/header.css">
<?php
}
?>
<script type="text/javascript" src="rs/js/util.js"></script>
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

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-59585522-2', 'auto');
  ga('send', 'pageview');

</script>
</head>

<body>
<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-M83DNM"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-M83DNM');</script>
<!-- End Google Tag Manager -->

<?php include_once("header.php");?>
<div id="main">
<section id="main_section">
<?php


if (! isset ($_GET["z"]))
    include ("main.php");
else {
    switch ($_GET["z"]){
        case "login":
            include ("usr/login.php");
            break;
        case "hosts":
            include ("usr/hosts.php");
            break;
        case "mod":
            include ("usr/modhost.php");
            break;
        case "del":
            include ("usr/delhost.php");
            break;
        case "remember":
            include ("usr/remember.php");
            break;
        case "newpassword":
            include ("usr/newpass.php");
            break;
        case "downloads":
            include ("downloads.php");
            break;
        case "usermod":
            include ("usr/user_actions.php");
            break;
	case "guides":
	    include ("pub/index.php");
	    break;
        default:
            include ("main.php");
            break;
    }
}
?>

</section>
<div id ="ajax_message_wrapper">
<script type="text/javascript">
    function ajax_message_tweak(val){
        ajax_message_wrapper.style["max-height"] = val+"px";
        return false;
    }
</script>
    <a href="#" id ="ajax_message_close" onclick="ajax_message_tweak(0);return false;">Cerrar</a>
    <div id="ajax_message" onchange="ajax_message_tweak(200);return false;"></div>

</div>
</div>
</body>

</html>
