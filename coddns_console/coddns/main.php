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

require_once (__DIR__ . "/include/config.php");
require_once (__DIR__ . "/include/functions_util.php");
require_once (__DIR__ . "/lib/graphs.php");
require_once (__DIR__ . "/lib/coduser.php");

if (! defined("_VALID_ACCESS")) { // Avoid direct access
    header ("Location: " . $config["html_root"] . "/");
    exit (1);
}

$auth_level_required = get_required_auth_level('','main','');
$user = new CODUser();
$user->check_auth_level($auth_level_required);

session_start();
if (!isset($_SESSION["lan"])){
    $_SESSION["lan"] = "es";
}
$lan = $_SESSION["lan"];
session_write_close();

?>

<!DOCTYPE html>

<html>

<head>
<title>Overall page</title>
<link rel="stylesheet" type="text/css" href="rs/css/pc/main_overall.css">
<script src='<?php echo $config["html_root"] . "/rs/js/external/chart.js/Chart.js";?>'></script>
</head>

<body>

<?php
if ($user->get_is_logged()){
    // USER ZONE - OVERALL STATUS VIEW

$dbclient = new DBClient($db_config);


/******************************************************
 * GRAPHS
 *  1st: zone usage
 *  2st: zone general statistics (require coddns_core)
 ******************************************************/

/**
 * 1st GRAPH: Zone usage
 */
// build data for zones per server chart:
$dbclient->connect() or die($dbclient->lq_error());
$q = "select s.id,s.tag,(select count(*) from zones z where z.server_id=s.id ) as nzones from servers s;";
$results = $dbclient->exeq($q) or die ($dbclient->lq_error());

$chart_pie["title"]  = "Zonas por servidor";
$chart_pie["id"]     = "zone_usage";
$chart_pie["width"]  = "150";
$chart_pie["height"] = "250";
$chart_pie["legend_style"] = "width: 150px;";

$labels   = "";
$data     = "";
$bgColor  = "";
$hbgColor = "";

while ($r = $dbclient->fetch_object($results)) {
    $labels   .= "'" . $r->tag . "',";
    $data     .= $r->nzones . ",";
    $bgColor  .= "getNextColor(),";
    $hbgColor .= "getCurrentShape(),";
}
$chart_pie["data"]["labels"]           = rtrim($labels, ",");
$chart_pie["data"]["datasets"]["data"] = rtrim($data, ",");
$chart_pie["data"]["datasets"]["backgroundColor"]      = rtrim($bgColor, ",");
$chart_pie["data"]["datasets"]["hoverBackgroundColor"] = rtrim($hbgColor, ",");

/* --------------------------------------------------------------------------- */
/* --------------------------------------------------------------------------- */


// Build data for linear graph

$chart_linear["title"]  = "Estad&iacute;sticas por zona";
$chart_linear["id"]     = "zone_stats";
$chart_linear["width"]  = "600";
$chart_linear["height"] = "400";
$chart_linear["legend_style"] = "width: 600px;";

// get all block elements dependent on zones:
$q = "select b.id_block,z.domain from stats_szb b, zones z where b.id_zone=z.id;";
$r_blocks = $dbclient->exeq($q) or die($dbclient->lq_error());

$i = 0; // initialize 'series' counter

$utime_init = time() - 86400;
$time_interval = 300;
$now = time();

$chart_linear["labels"] = "";
for ($x = 0; $x <= ($now-$utime_init); $x+=$time_interval) {
    $chart_linear["labels"] .= "'" . date("Ymd H:i:s", $utime_init + $x) . "'";
    if ($x < $now){
        $chart_linear["labels"] .= ",";
    }
}

while ($row_blocks = $dbclient->fetch_array ($r_blocks)) {
    // get all items dependent on those block elements
    $q = "select id, tag from stats_item where id_block=" . $row_blocks["id_block"] . " and tag like '%auth%';";
    $r_items = $dbclient->exeq($q) or die($dbclient->lq_error());

    while ($row_items = $dbclient->fetch_array ($r_items)) {
        // get all final data
        $q = "select * from (select * from stats_data where id_item=" . $row_items["id"] . " and utimestamp >= " . $utime_init . " order by utimestamp desc) sub order by utimestamp asc;";
        $r_data = $dbclient->exeq($q) or die($dbclient->lq_error());

        $d[$i]["label"] = $row_items["tag"];
        $d[$i]["data"]  = "";
        $d[$i]["backgroundColor"] = "getNextShape()";
        $d[$i]["borderColor"]     = "getCurrentShape()";

        $column = 0;
        while ($row_data = $dbclient->fetch_array ($r_data)) {
            // organize
            $current_time_segment_floor = $utime_init + (($column) * $time_interval);
            while ( $current_time_segment_floor < $now ){
                // calculate segment
                $current_time_segment_floor = $utime_init + (($column) * $time_interval);
                $current_time_segment_ceil  = $utime_init + (($column+1) * $time_interval);

                // check data in segment (column)
                if ( ( $current_time_segment_ceil  >  $row_data["utimestamp"] )
                  && ( $current_time_segment_floor <= $row_data["utimestamp"] ) ) {
                    // if the value is in the "segment" of time is represented in the graph, add the data:
                    $d[$i]["data"] .= $row_data["value"] . ",";
                    $column++;
                    break;
                }
                else{
                    // else set to null, and skip this segment
                    $d[$i]["data"] .= "null,";
                }
                $column++;
            }
        }
        $d[$i]["data"] = rtrim($d[$i]["data"], ",");

        $d[$i]["data"] .= "";
        // Next dataset
        $i++;
    }
}
if(isset($d)){
    $chart_linear["datasets"] = $d;
}





?>

<section style="margin-bottom: 20px; text-align: justify;">
    <h1>Inicio</h1>
    <article>
        <h3>Resumen general</h3>
        <div class="chart_wrapper">
            <div class="graph_wrapper">
                <?php
                    echo print_graph_pie($chart_pie);
                ?>
            </div>
            <div class="graph_wrapper">
                <?php
                    echo print_graph_line($chart_linear);
                ?>
            </div>

        </div>

    </article>
    <article class="nav">
        <h3>Navegaci&oacute;n r&aacute;pida</h3>

        <div class="mtr_like">
            <a href="<?php echo $config["html_root"] . "/?m=usr&z=hosts"; ?>">Gestionar hosts</a>
            <?php
            if ($user->is_global_admin()) {
            ?>
            <a href="<?php echo $config["html_root"] . "/?m=adm&z=center#servers"; ?>">Admininistrar servidores</a>
            <a href="<?php echo $config["html_root"] . "/?m=adm&z=site#users"; ?>">Admininistrar el sitio</a>
            <?php
            }
            ?>
            
        </div>
        
    </article>
</section>

<?php
}
else {
    // PUBLIC ZONE

    /* CASTELLANO */
    $text["es"]["main_reg"]    = "Registrarme";
    $text["es"]["main_acc"]    = "Acceder";
    $text["es"]["ph_email"]    = "correo electr&oacute;nico";
    $text["es"]["ph_pass"]     = "password";
    $text["es"]["ph_cpass"]    = "confirma password";
    $text["es"]["f_send"]      = "Enviar";
    $text["es"]["label_cpass"] = "Confirma password:";
    $text["es"]["remember"]    = "&iquest;Olvid&oacute; su contrase&ntilde;a?";

    /* ENGLISH */
    $text["en"]["main_reg"]    = "Sign in";
    $text["en"]["main_acc"]    = "Log in";
    $text["en"]["ph_email"]    = "email";
    $text["en"]["ph_pass"]     = "password";
    $text["en"]["ph_cpass"]    = "confirm password";
    $text["en"]["f_send"]      = "Send";
    $text["en"]["label_cpass"] = "Confirm password:";
    $text["en"]["remember"]    = "Did you forgot your password?";


?>
<section>
    <article>
    <h2>Bienvenido a <b>coddns</b></h2>

    <p>Acceda con su cuenta para empezar a utilizar el sistema.</p>
    <p>En caso de no disponer a&uacute;n de una cuenta v&aacute;lida puede registrarse <a href="<?php echo $config["html_root"] . "/?m=usr&z=users&op=login"?>">aqu&iacute;</a></p>
    </article>

</section>
<section id="login">
    <h3><?php echo $text[$lan]["main_acc"];?></h3>
    <form id="loginf" method="POST" action="usr/users/users_rq_login.php" onsubmit="fsgo('loginf', 'login_response','usr/users/users_rq_login.php', true);return false;">
    <ul>
        <li>
            <label>E-mail: </label>
            <input type="email" name="u" placeholder="<?php
                echo $text[$lan]["ph_email"];
            ?>" required />
        </li>
        <li>
            <label>Password: </label>
            <input type="password" name="p" id="p" placeholder="password" required/>
        </li>
        <li>
            <a id="remember" href="<?php echo $config["html_root"];?>/?m=usr&z=users&op=remember&lang=<?php echo $lan;?>"><?php echo $text[$lan]["remember"];?> </a>
            <input type="submit" value="<?php
                echo $text[$lan]["f_send"];
            ?>"/>
        </li>
    </ul>
    <div id="login_response"></div>
    </form>
</section>

<?php
}

if ((isset($dbclient)) && ($dbclient->is_connected())) {
    $dbclient->disconnect();
}

?>
</body>
</html>
