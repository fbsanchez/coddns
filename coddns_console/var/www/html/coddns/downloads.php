<?php
if (!isset($lan))
    header ("Location: /");

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
    <a href="/?lang=<?php echo $lan;?>"><?php echo $text[$lan]["back"];?></a>
</section>
