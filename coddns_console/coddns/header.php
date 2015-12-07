<?php


?>

<header>

Hola, soy la nueva estructura para el header

</header>
<?php 
/* old header
<header>
<div class="img" onclick="window.location='<?php echo $config["html_root"];?>/?lang=<?php echo $lan;?>';">&nbsp;</div>
<div class="text">
        <h1>Custom Open Dynamic DNS <span style="font-size:0.5em;font-style:italic;">[rc1]</span></h1>
    <h2><?php echo $text[$lan]["welcome"]; ?></h2>
    <p><?php echo $text[$lan]["yourip"] . " " . _ip(); ?> </p>
</div>
<div id="navigation" class="relative">
    <nav>
        <a class="pl<?php if ((! isset ($_GET["z"])) || ($_GET["z"] == "hosts")  || ($_GET["z"] == "remember") ) echo " pl_select";?>"
              href="<?php echo $config["html_root"];?>/?lang=<?php echo $lan;?>"> <?php echo $text[$lan]["start"]; ?></a>
        <a class="pl<?php if (( isset ($_GET["z"])) && ($_GET["z"] == "downloads")) echo " pl_select";?>"
              href="<?php echo $config["html_root"];?>/?lang=<?php echo $lan;?>&z=downloads"><?php echo $text[$lan]["downloads"];?></a>
    <?php if (isset ($_SESSION["email"])) {?>
        <a class="pl<?php if (( isset ($_GET["z"])) && ($_GET["z"] == "usermod")) echo " pl_select";?>"
              href="<?php echo $config["html_root"];?>/?lang=<?php echo $lan;?>&z=usermod"><?php echo $text[$lan]["nav_account"];?></a>
        <a class="pl" href="logout.php"><?php echo $text[$lan]["nav_logout"]; ?></a>
    <?php }?>

        <div class="lang">
            <a href="<?php echo $config["html_root"];?>/?lang=es">
                <img src="<?php echo $config["html_root"];?>/rs/img/es.png" alt="es"/>
            </a>
            <a href="<?php echo $config["html_root"];?>/?lang=en">
                <img src="<?php echo $config["html_root"];?>/rs/img/en.png" alt="en"/>
            </a>
<!--
            <a href="<?php echo $config["html_root"];?>/?lang=de">
                <img src="<?php echo $config["html_root"];?>/rs/img/de.png" alt="de"/>
            </a>
-->
        </div>
    </nav>
</div>
</header>
?>
