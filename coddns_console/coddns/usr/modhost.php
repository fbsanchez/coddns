<?php

include_once(dirname(__FILE__) . "/../include/config.php");
require_once(dirname(__FILE__) . "/../include/config.php");
require_once(dirname(__FILE__) . "/../lib/ipv4.php");

session_start();
if (!isset($_SESSION["lan"])){
    $_SESSION["lan"] = "es";
}
$lan = $_SESSION["lan"];
session_write_close();

if (! defined("_VALID_ACCESS")) {
    header ("Location: " . $config["html_root"] . "/?z=hosts&lang=" . $lan);
    exit (1);
}

if ( (! isset($_SESSION["email"])) || (!isset ($_POST["edith"])) || (! isset ($_POST["editip"]))  ){
    header ("Location: " . $config["html_root"] . "/?z=hosts&lang=" . $lan);
    exit (1);
}

?>
<!DOCTYPE html>
<html>

<head>
<script type="text/javascript">
function select_my_ip(){
    nip.value="<?php echo _ip();?>";
}
</script>
</head>

<body>
<section>
<a href="<?php echo $config["html_root"] . "/?z=hosts&lang=" . $lan;?>"><?php echo $text[$lan]["back"];?></a>
<form id="modhost" onsubmit="return false;" method="POST" action="?z=hosts" onsubmit="return false;">
    <ul>
        <li>
            <label>Host:</label><input style="border: none; font-size: 1em;text-align: right;" type="text" readonly name="edith" value="<?php echo $_POST["edith"]; ?>"></input>
        </li>
        <li>
            <label>IP actual: </label><span style="float: right;"><?php echo $_POST["editip"];?></span>
        </li>
        <li>
            <label>Nueva IP: </label><input style="text-align: right;" type="text" name="nip" id="nip" value="<?php echo $_POST["editip"];?>"></input>
        </li>
        </li>
            <a style="padding: 5px; font-size: 0.8em;" href="#" onclick="select_my_ip();return false;">Coger mi IP actual</a>
        </li>
        <li>
            <input type="submit" value="Actualizar" onclick="fsgo('modhost', 'ajax_message','usr/rq_modhost.php', true,raise_ajax_message);return false;"/>
        </li>
    </ul>
</form>
</section>
</body>

</html>
