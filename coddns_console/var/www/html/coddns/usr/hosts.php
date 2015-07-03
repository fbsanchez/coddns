<?php
require_once("include/config.php");
require_once("lib/pgclient.php");


if (! isset ($_SESSION["email"])) {
    header ("Location: " . $config["html_root"] . "/");
    exit (1);
}
if( !isset($_SESSION["lan"]) ){
    session_write_close();
    header ("Location: " . $config["html_root"] . "/?lang=es");
    exit (1);
}

$lan = $_SESSION["lan"];

/* CASTELLANO */
$text["es"]["hosts_welcome"] = "
    <h2>Gestor de etiquetas</h2>
    <p>Aqu&iacute; puedes agregar nuevas etiquetas para tus dispositivos (<i>hosts</i>) o administrar las existentes.</p>
    <p>Recuerda que la responsabilidad sobre los contenidos que abras a Internet es s&oacute;lo tuya.</p>
";
$text["es"]["ht_hname"]      = "Nombre de host";
$text["es"]["label_tag"]     = "Etiqueta:";
$text["es"]["label_ip"]      = "Direcci&oacute;n IP:";
$text["es"]["label_getip"]   = "Coger mi IP";
$text["es"]["f_add"]         = "Agregar";
$text["es"]["ht_htitle"]     = "Mis hosts";
$text["es"]["dberror"]       = "Wooops, contacte con el administrador";

/* ENGLISH */
$text["en"]["hosts_welcome"] = "
    <h2>Tag manager</h2>
    <p>Here you can add new tags for your devices (<i>hosts</i>) or manage them.</p>
    <p>Remember this: The responsability over the content you have opened to Internet is only yours.</p>
";
$text["en"]["ht_hname"]      = "Hostname";
$text["en"]["label_tag"]     = "Tag:";
$text["en"]["label_ip"]      = "IP address:";
$text["en"]["label_getip"]   = "Select my IP";
$text["en"]["f_add"]         = "Add";
$text["en"]["ht_htitle"]     = "My hosts";
$text["en"]["dberror"]       = "Wooops, please contact the administrator at footer";

/* DEUTSCH */
$text["de"]["hosts_welcome"] = "
    <h2>Tag manager</h2>
    <p>Here you can add new tags for your devices (<i>hosts</i>) or manage them.</p>
    <p>Remember this: The responsability over the content you have opened to Internet is only yours.</p>
";
$text["de"]["ht_hname"]      = "Hostname";
$text["de"]["label_tag"]     = "Tag:";
$text["de"]["label_ip"]      = "IP address:";
$text["de"]["label_getip"]   = "Select my IP";
$text["de"]["f_add"]         = "Add";
$text["de"]["ht_htitle"]     = "My hosts";
$text["de"]["dberror"]       = "Wooops, please contact the administrator at footer";


?>
<!DOCTYPE html>

<html>
<head>
    <title>resultados</title>
    <style type="text/css">
        table {
            padding: 5px;
            margin: 0 auto;
            border-collapse: collapse;
        }
        thead{
            text-align: center;
            background: #133445;
            color: white;
        }
        thead td {
            padding: 5px;
        }
        tbody *{
            padding: 5px;
        }
        td {
            border: 1px solid #ddd;
        }
        tbody tr:hover{
            background: #E8F1F9;
        }
        button, input[type="submit"]{
            padding-left: 5px;
            padding-right: 5px;
        }
        td.del{
            cursor: pointer;
            background: url('<?php echo $config["html_root"];?>/rs/img/delete.png') no-repeat center;
            background-size: 1em;
            width: 1.4em;
            height: 1.4em;
        }
        td.edit{
            cursor: pointer;
            background: url('<?php echo $config["html_root"];?>/rs/img/edit.png') no-repeat center;
            background-size: 1em;
            width: 1.4em;
            height: 1.4em;
        }
    </style>
<script>
var t="";
function checkHostName(obj){
    if(/^([a-zA-Z]+([0-9]*[a-zA-Z]*)*)/.test(obj.value))
        updateContent("rec_info", "rest_host.php", "h="+obj.value);
    return false;
}
function select_my_ip(){
    ip.value="<?php echo _ip();?>";
    return false;
}

</script>
</head>

<body>
<section style="text-align: justify; margin-bottom: 20px;">
<?php
echo $text[$lan]["hosts_welcome"];
?>
</section>
<section class="uarea">
    <form id="newhost" method="POST" action="usr/rq_nhost.php">
    <ul>
        <li>
            <label><?php echo $text[$lan]["label_tag"];?></label>
        </li>
        <li>
            <input type="text" id="h" name="h" onchange="checkHostName(this);return false;" pattern="^([a-zA-Z]+([0-9]*[a-zA-Z]*)*)" required/><i class="extension">.<?php echo $config["domainname"]?></i>
            <div id="rec_info"></div>
        </li>
            <li>
            <label><?php echo $text[$lan]["label_ip"];?></label>
        </li>
        <li>
            <input type="text" id="ip" name="ip" value="<?php echo _ip();?>" required/> <button onclick="select_my_ip(); return false;"><?php echo $text[$lan]["label_getip"];?></button>
        </li>
        <li>
            <label></label>
            <input type="submit" value="<?php echo $text[$lan]["f_add"]; ?>"/>
        </li>
    </ul>
    </form>
</section>

<div id="myhosts">


<?php
$pgclient = new PgClient($db_config);

$pgclient->connect() or die($text[$lan]["dberror"]);

$q = "select tag, ip from hosts where oid=(select id from usuarios where mail='" . $_SESSION["email"] . "');";
$r = $pgclient->exeq($q);

?>
<h3><?php echo $text[$lan]["ht_htitle"];?></h3>
<form id="change" action="<?php echo $config["html_root"];?>/?z=mod" method="POST">
    <input type="hidden" id="edith" name="edith" required/>
    <input type="hidden" id="editip" name="editip" required/>
</form>
<form id="del" action="<?php echo $config["html_root"];?>/?z=del" method="POST">
    <input type="hidden" id="delh" name="delh" required/>
</form>

<table>
    <thead>
        <tr>
            <td><?php echo $text[$lan]["ht_hname"];?></td>
            <td>IP</td>
            <td colspan="2">Ops.</td>
        </tr>
    </thead>
    <tbody>
<?php
while ($row = pg_fetch_array ($r)) {
    echo "<tr><td>" . $row["tag"] . "</td><td>" . $row["ip"] . "</td><td class='edit' title='editar' onclick=\"editip.value='" . $row["ip"] . "';edith.value='" . $row["tag"] . "';change.submit();\"></td><td class='del' title='eliminar' onclick=\"delh.value='" . $row["tag"] . "'; if (confirm('Seguro que desea eliminar " . $row["tag"] . "?')) {del.submit();}\"></td></tr>";
}
?>
    </tbody>
</table>
</div>
</body>
</html>
<?php

?>
