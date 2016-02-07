<?php

include_once (dirname(__FILE__) . "/include/config.php");

if (!defined("_VALID_ACCESS")){
    header("Location: " . $config["html_root"] . "/");
    die ("Unauthorized");
}


$start_menu_class    = "pl";
$menu_item_downloads = "pl";
$menu_item_usermod   = "pl";
$menu_item_user      = "pl";
$menu_item_priv_zone = "pl";
$menu_item_logout    = "pl";
$menu_item_pub       = "pl";


session_start();
if (!isset($_SESSION["lan"])){
    $_SESSION["lan"] = "es";
}
$lan = $_SESSION["lan"];
session_write_close();


if (! isset ($_GET["z"])){
    $start_menu_class = "pl_select";
}
else {
    switch ($_GET["z"]) {
        case "remember":
            $start_menu_class = "pl_select";
            break;
        case "downloads":
            $menu_item_downloads = "pl_select";
            break;
        case "usermod":
            $menu_item_user = "pl_select";
            break;
        case "hosts":
        case "del":
        case "mod":
        case "login":
            $menu_item_priv_zone = "pl_select";
            $menu_item_usermod = "pl_select";
            break;
        case "pub":
            $menu_item_pub = "pl_select";
            break;
        default:
            $start_menu_class = "pl_select";
            $menu_item_downloads = "pl";
            $menu_item_usermod = "pl";
            $menu_item_user = "pl";
            $menu_item_priv_zone = "pl";
            break;
    }
}


?>
<script type="text/javascript">
function red(id,zone,page){
    menu_item_main.className="pl";
    menu_item_policy.className="pl";
    menu_item_cookies.className="pl";
    menu_item_priv_zone.className="pl";
    menu_item_downloads.className="pl";
    menu_item_pub.className="pl";
<?php if (get_user_auth()) {
?>
    menu_item_user.className="pl";
<?php
}
?>
	id.className="pl_select";
	updateContent(zone,page);
}
</script>
<header id="header">
<div id="launcher" class="box-shadow-menu" onclick="minimize_menu();return false;">
</div>
<a href="<?php echo $config["html_root"];?>/"><img src="<?php echo $config["html_root"];?>/rs/img/coddns_225.png" alt="logo"></a>
<div id="menu">
    <ul>
        <li><a id="menu_item_main"      class="<?php echo $start_menu_class;?>"     href="<?php echo $config["html_root"];?>/">Inicio</a></li>
        <li><a id="menu_item_downloads" class="<?php echo $menu_item_downloads;?>" href="<?php echo $config["html_root"];?>/?z=downloads">Descargas</a></li>
        
<?php
if (file_exists('cms/')) {
?>
        <li><a id="menu_item_pub" class="<?php echo $menu_item_pub;?>" href="<?php echo $config["html_root"];?>/?z=pub">Documentaci&oacute;n</a></li>
<?php
}
if (get_user_auth()) {
?>

        <li><a id="menu_item_priv_zone" class="<?php echo $menu_item_priv_zone;?>"  href="<?php echo $config["html_root"];?>/?z=hosts">&Aacute;rea personal</a></li>
        <li><a id="menu_item_user"      class="<?php echo $menu_item_user;?>"       href="<?php echo $config["html_root"];?>/?z=usermod">Mi cuenta</a></li>
        <li><a id="menu_item_logout"    class="<?php echo $menu_item_logout;?>"     href="<?php echo $config["html_root"];?>/logout.php">Desconectarme</a></li>
<?php
}
else {
?>
        <li><a id="menu_item_priv_zone" class="<?php echo $menu_item_priv_zone;?>"  href="<?php echo $config["html_root"];?>/?z=login">&Aacute;rea personal</a></li>
<?php
}
?>
    </ul>
</div>
<div id="contact">
    <ul>
        <li>
            <a id="menu_item_policy"  href="#" class="pl" onclick="red(this,'main_section','cpolicy.html');"><?php echo $text[$lan]["cookie_policy"];?></a>
        </li>
        <li>
            <a id="menu_item_cookies" href="#" class="pl" onclick="red(this,'main_section','terms.html');"><?php echo $text[$lan]["terms"];?></a>
        </li>
    </ul>
    <div style="display:inline-block;">
    <a target="_new" title="Fco de Borja S&aacute;nchez" href='https://plus.google.com/104344930735301242497/about'>
        <div class="pic" style="background: url('<?php echo $config["html_root"];?>/rs/img/gp.png') #DA4835 no-repeat center;background-size:41px;"></div>
    </a>
    </div>
    <div style="display: inline-block;margin-left: 15px;">
    <a target="_new2" title="CODDNS en GitHub" href='https://github.com/fbsanchez/coddns'>
        <div class="pic" style="background: url('<?php echo $config["html_root"];?>/rs/img/github.png') #FFF no-repeat center;background-size:41px;"></div>
    </a>
    </div>
</div>
</header>

