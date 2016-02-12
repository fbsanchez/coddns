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

require_once (dirname(__FILE__) . "/../include/config.php");
require_once (dirname(__FILE__) . "/../lib/db.php");
require_once (dirname(__FILE__) . "/../lib/ipv4.php");
require_once (dirname(__FILE__) . "/../lib/util.php");
require_once (dirname(__FILE__) . "/../lib/coduser.php");

if (! defined("_VALID_ACCESS")) { // Avoid direct access
    header ("Location: " . $config["html_root"] . "/");
    exit (1);
}

$auth_level_required = get_required_auth_level('usr','hosts','mod');
$user = new CODUser();
$user->check_auth_level($auth_level_required);


session_start();
if (!isset($_SESSION["lan"])){
    $_SESSION["lan"] = "es";
}
$lan = $_SESSION["lan"];
session_write_close();

if (  (!isset ($_POST["edith"])) || (! isset ($_POST["editip"]))  ){
    header ("Location: " . $config["html_root"] . "/?z=hosts&lang=" . $lan);
    exit (1);
}

$dbclient= new DBClient($db_config);
$dbclient->connect() or die ("ERR");


$host = strtok($_POST["edith"],".");
$main = strtok(".");
$dom  = strtok(".");

$check = $config["domainname"];
$checkm = strtok ($check, ".");
$checkd = strtok (".");

if(    ( $main != $checkm )
    || ( $dom  != $checkd )
    || ( strlen($host) < MIN_HOST_LENGTH )
    || ( strlen($host) > MAX_HOST_LENGTH ))
    die ("ERR: nombre de host no valido");
$host =  $dbclient->prepare($host, "letters") . "." . $config["domainname"];

$q   = "select ip from hosts where tag='$host';";
$r   = $dbclient->exeq($q);
$obj = $dbclient->fetch_object($r);

if(!isset($obj)){
	die ("ERR: Consulta erronea");
}

$ip = long2ip($obj->ip);

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
<a href="<?php echo $config["html_root"] . "/?m=usr&z=hosts&lang=" . $lan;?>"><?php echo $text[$lan]["back"];?></a>
<form id="modhost" onsubmit="return false;" method="POST" action="?m=usr&z=hosts" onsubmit="return false;">
    <ul>
        <li>
            <label>Host:</label><input style="border: none; font-size: 1em;text-align: right;" type="text" readonly name="edith" value="<?php echo $host; ?>"></input>
        </li>
        <li>
            <label>IP actual: </label><span style="float: right;"><?php echo $ip?></span>
        </li>
        <li>
            <label>Nueva IP: </label><input style="text-align: right;" type="text" name="nip" id="nip" value="<?php echo $_POST["editip"];?>"></input>
        </li>
        </li>
            <a style="padding: 5px; font-size: 0.8em;" href="#" onclick="select_my_ip();return false;">Coger mi IP actual</a>
        </li>
        <li>
            <input type="submit" value="Actualizar" onclick="fsgo('modhost', 'ajax_message','usr/hosts_rq_mod.php', true,raise_ajax_message);return false;"/>
        </li>
    </ul>
</form>
</section>
</body>

</html>
