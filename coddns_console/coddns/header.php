<?php

include_once (dirname(__FILE__) . "/include/config.php");

$start_menu_class     = "pl";
$downloads_menu_class = "pl";
$usermod_menu_class   = "pl";
$menu_item_user       = "pl";
$menu_item_priv_zone  = "pl";
$menu_item_logout     = "pl";

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
            $downloads_menu_class = "pl_select";
            break;
        case "usermod":
            $menu_item_user = "pl_select";
            break;
        case "hosts":
        case "del":
        case "mod":
        case "login":
            $menu_item_priv_zone = "pl_select";
            $usermod_menu_class = "pl_select";
            break;
        default:
            $start_menu_class = "pl_select";
            $downloads_menu_class = "pl";
            $usermod_menu_class = "pl";
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
<a href="<?php echo $config["home_html"];?>/"><img src="/rs/img/coddns_225.png" alt="logo"></a>
<div id="menu">
    <ul>
        <li><a id="menu_item_main"      class="<?php echo $start_menu_class;?>"     href="<?php echo $config["html_root"];?>/">Inicio</a></li>
        <li><a id="menu_item_downloads" class="<?php echo $downloads_menu_class;?>" href="<?php echo $config["html_root"];?>/?z=downloads">Descargas</a></li>
<?php
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
    <div style="float:left;">
    <a target="_new" title="Fco de Borja S&aacute;nchez" href='https://plus.google.com/104344930735301242497/about'>
        <div class="pic" style="background: url('<?php echo $config["html_root"];?>/rs/img/gp.png') #DA4835 no-repeat center;"></div>
    </a>
    </div>
    <div style="float:right;">
    <a target="_new2" title="CODDNS en GitHub" href='https://github.com/fbsanchez/coddns'>
        <div class="pic" style="background: url('<?php echo $config["html_root"];?>/rs/img/github.png') #FFF no-repeat center;"></div>
    </a>
    </div>
</div>
</header>

