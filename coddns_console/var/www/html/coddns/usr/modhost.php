<?php
require_once("include/config.php");
require_once ("lib/ipv4.php");

if ( (! isset($_SESSION["email"])) || (!isset ($_POST["edith"])) || (! isset ($_POST["editip"]))  ){
    header ("Location: " . $config["html_root"] . "/");
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
<a href="<?php echo $config["html_root"]?>/?lang=<?php echo $lan;?>"><?php echo $text[$lan]["back"];?></a>
<form onsubmit="return false;" method="POST" action="<?php echo $config["html_root"];?>/usr/rq_modhost.php">
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
            <input type="submit" value="Actualizar" onclick="submit();"/>
        </li>
    </ul>
</form>
</section>
</body>

</html>
