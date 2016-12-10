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

require_once (__DIR__ . "/../include/config.php");
require_once (__DIR__ . "/../lib/util.php");
require_once (__DIR__ . "/../lib/coduser.php");

if (! defined("_VALID_ACCESS")) { // Avoid direct access
    header ("Location: " . $config["html_root"] . "/");
    exit (1);
}

$auth_level_required = get_required_auth_level('','downloads','');
$user = new CODUser();
$user->check_auth_level($auth_level_required);

session_start();
if (!isset($_SESSION["lan"])){
    $_SESSION["lan"] = "es";
}
$lan = $_SESSION["lan"];
session_write_close();

/* CASTELLANO */
$text["es"]["d_title"] = "Descargas";
$text["es"]["d_linux_header"]  ="Puedes descargar el cliente de actualizaci&oacute;n de DNS din&aacute;mico para Linux de ";
$text["es"]["d_link"]    ="aqu&iacute;";
$text["es"]["d_windows_header"]  ="Puedes descargar el instalador del cliente de actualizaci&oacute;n de DNS din&aacute;mico para Windows de ";

/* ENGLISH */
$text["en"]["d_title"]          ="Downloads";
$text["en"]["d_linux_header"]   ="You can download the dynamic DNS updater client for Linux from ";
$text["en"]["d_link"]           ="here";
$text["en"]["d_windows_header"] ="You can download the dynamic DNS updater client installer for Windows from ";

/* DEUTCH */
$text["de"]["d_title"]          ="Downloads";
$text["de"]["d_linux_header"]   ="You can download the dynamic DNS updater client for Linux from ";
$text["de"]["d_link"]           ="here";
$text["de"]["d_windows_header"] ="You can download the dynamic DNS updater client installer for Windows from ";

?>
<section>
    <h2><?php echo $text[$lan]["d_title"];?></h2>
    <h3>Linux</h3>
    <p><?php echo $text[$lan]["d_linux_header"];?><a href="downloads/ddns_updater_Linux.tar.gz"><?php echo $text[$lan]["d_link"];?></a></p>
    <br>
    <br>
    <h3>Windows</h3>
    <p><?php echo $text[$lan]["d_windows_header"];?><a href="downloads/ddns_updater_Windows.zip"><?php echo $text[$lan]["d_link"];?></a></p>
    <br>
    <br>
</section>
