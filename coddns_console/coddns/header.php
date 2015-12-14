<?php


?>

<header>
<div id="launcher" class="box-shadow-menu">

</div>
<div id="menu">

    <ul>
        <li><a href="<?php echo $config["html_root"];?>">Inicio</a></li>
        <li>Descargas</li>
        <li><a href="<?php echo $config["html_root"];?>/?z=hosts">&Aacute;rea personal</a></li>
        <li>
            <a href='https://plus.google.com/104344930735301242497/about' target="_new">
                <img title="Fco de Borja S&aacute;nchez" class="rrss" src="<?php echo $config["html_root"];?>/rs/img/gp.png" alt="gp">
            </a>
        </li>
        <li>
            <a href="cpolicy.html"><?php echo $text[$lan]["cookie_policy"];?></a>
        </li>
        <li>
            <a href="terms.html"><?php echo $text[$lan]["terms"];?></a>
        </li>
    </ul>

</div>

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

*/
?>

