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

require_once __DIR__ . "/include/config.php";
require_once __DIR__ . "/lib/coduser.php";

if (!defined("_VALID_ACCESS")) {
    header("Location: " . $config["html_root"] . "/");
    die("Unauthorized");
}

function check_show($user, $mode, $zone, $operation)
{
    $auth_level_required = get_required_auth_level($mode, $zone, $operation);

    if ($user->get_auth_level() >= $auth_level_required) {
        return true;
    }
    return false;
}

try {
    $user = new CODUser();
    $user->check_auth_level(get_required_auth_level(null, "header", null));
} catch (Exception $e) {
    echo $e->getMessage();
    exit(1);
}


session_start();
if (!isset($_SESSION["lan"])) {
    $_SESSION["lan"] = "es";
}
$lan = $_SESSION["lan"];
session_write_close();


// initialize menu_item_class
$menu_item_main      = "pl";
$menu_item_downloads = "pl";
$menu_item_usermod   = "pl";
$menu_item_user      = "pl";
$menu_item_priv_zone = "pl";
$menu_item_logout    = "pl";
$menu_item_pub       = "pl";
$menu_item_adm       = "pl";
$menu_item_contact   = "pl";

// initialize printer id flags
$enable_main        = 0;
$enable_downloads   = 0;
$enable_cms         = 0;
$enable_hosts       = 0;
$enable_users_mod   = 0;
$enable_adm         = 0;
$enable_logout      = 0;
$enable_users_login = 0;
$enable_contact     = 1;

if ($url == "index.php") {
    $menu_item_main = "pl_select";
} else {
    switch ($mode) {
        case "adm":
            $menu_item_adm = "pl_select";
            break;
        case "usr":
            switch ($zone) {
                case "users":
                    switch ($operation) {
                        case "login":
                        case "signin":
                        case "remember":
                            $menu_item_priv_zone = "pl_select";
                            break;
                        case "mod":
                            $menu_item_user = "pl_select";
                            break;
                        case "resetpass":
                            break;
                        case "sendtoken":
                            break;
                        default:
                            break;
                    }
                    break;
                case "hosts":
                    switch ($operation) {
                        case "mod":
                        case "new":
                        case "del":
                        default:
                            $menu_item_priv_zone = "pl_select";
                            break;
                    }
                    break;
            }
            break;
        case "cms":{
            $menu_item_pub = "pl_select";
            break;
        }
        case null:
            switch ($zone) {
                case "downloads":
                    $menu_item_downloads = "pl_select";
                    break;
                case "cliupdate":
                    $menu_item_policy = "pl_select";
                    break;
                case "cookie_policy":
                    $menu_item_cookies = "pl_select";
                    break;
                case "contact":
                    $menu_item_contact = "pl_select";
                    break;
                    
                default:
                    $menu_item_main = "pl_select";
                    break;
            }
            break;
        default:
            $menu_item_main = "pl_select";
            break;
    }
}


?>
<header id="header" style="width:0;">
<div id="launcher" class="box-shadow-menu" onclick="toggle_menu();return false;">
</div>
<a href="<?php echo $config["html_root"];?>/"><img src="<?php echo $config["html_root"];?>/rs/img/coddns_225.png" alt="logo"></a>
<div id="menu">
    <ul>

<?php
    
if (check_show($user, null, "main", null)) {
    $enable_main = 1;
    ?>
        <li><a id="menu_item_main"      class="<?php echo $menu_item_main;?>"     href="<?php echo $config["html_root"];?>/">Home</a></li>
<?php } ?>

<?php
if (check_show($user, null, "downloads", null)) {
    $enable_downloads = 1;?>
        <li><a id="menu_item_downloads" class="<?php echo $menu_item_downloads;?>" href="<?php echo $config["html_root"];?>/?z=downloads">Downloads</a></li>
<?php } ?>

<?php
/*
if ((file_exists('cms/')) && (check_show($user,"cms",null,null))) {
    $enable_cms = 1;
?>
        <li><a id="menu_item_pub" class="<?php echo $menu_item_pub;?>" href="<?php echo $config["html_root"];?>/?m=cms">Documentaci&oacute;n</a></li>
<?php } */?>
<?php
if (check_show($user, "usr", "hosts", null)) {
    $enable_hosts = 1;
    ?>
        <li><a id="menu_item_priv_zone" class="<?php echo $menu_item_priv_zone;?>"  href="<?php echo $config["html_root"];?>/?m=usr&z=hosts">Host management</a></li>
<?php } ?>
<?php
if (check_show($user, "usr", "users", "mod")) {
    $enable_users_mod = 1;
    ?>
        <li><a id="menu_item_user"      class="<?php echo $menu_item_user;?>"       href="<?php echo $config["html_root"];?>/?m=usr&z=users&op=mod">My account</a></li>
<?php } ?>
<?php
if (check_show($user, "adm", null, null)) {
    $enable_adm = 1;
    ?>
        <li><a id="menu_item_adm"      class="<?php echo $menu_item_adm;?>"       href="<?php echo $config["html_root"];?>/?m=adm">Administration</a></li>
<?php } ?>
<?php
if (($user->get_is_logged()) && (check_show($user, null, "logout", null))) {
    $enable_logout = 1;
    ?>
        <li><a id="menu_item_logout"    class="<?php echo $menu_item_logout;?>"     href="<?php echo $config["html_root"];?>/?m=usr&z=users&op=logout">Log off</a></li>
<?php } ?>
<?php
if (($user->get_is_logged() == false) && (check_show($user, "usr", "users", "login"))) {
    $enable_users_login = 1;
    ?>
        <li><a id="menu_item_priv_zone" class="<?php echo $menu_item_priv_zone;?>"  href="<?php echo $config["html_root"];?>/?m=usr&z=users&op=login">Host management</a></li>
    <?php
}
?>
    </ul>
    <script type="text/javascript">
    function red(id,zone,page){
        <?php

        if ($enable_main) {
            echo "menu_item_main.className='pl';\n";
        }
        if ($enable_downloads) {
            echo "menu_item_downloads.className='pl';\n";
        }
        if ($enable_cms) {
            echo "menu_item_pub.className='pl';\n";
        }
        if ($enable_hosts) {
            echo "menu_item_priv_zone.className='pl';\n";
        }
        if ($enable_users_mod) {
            echo "menu_item_user.className='pl';\n";
        }
        if ($enable_adm) {
            echo "menu_item_adm.className='pl';\n";
        }
        if ($enable_users_login) {
            echo "menu_item_priv_zone.className='pl';\n";
        }
        ?>
        menu_item_contact.className='pl';
        menu_item_policy.className='pl';
        menu_item_cookies.className='pl';

        id.className="pl_select";
        updateContent(zone,page);
    }
    </script>
</div>
<div id="contact">
    <ul>
    <?php
    if (check_show($user, null, "logout", null)) {
        ?>
        <li><a id="menu_item_contact"    class="<?php echo $menu_item_contact;?>"     href="<?php echo $config["html_root"];?>/?z=contact">Contact</a></li>
    <?php } ?>

        <li>
            <a id="menu_item_policy"  href="#" class="pl" onclick="red(this,'main','cpolicy.html');">Cookie policy</a>
        </li>
        <li>
            <a id="menu_item_cookies" href="#" class="pl" onclick="red(this,'main','terms.html');">Terms of service</a>
        </li>
    </ul>
</div>

<script type="text/javascript">
    header = document.getElementById("header");
    var visible_menu = <?php
        global $config;

    if (isset($config["session"]["visible_menu"])) {
        echo $config["session"]["visible_menu"];
    } else {
        echo "1";
    }?>;
    if (visible_menu == 0) {
        header.className = "minimized";
        
    }
    header.removeAttribute("style");
</script>
</header>

