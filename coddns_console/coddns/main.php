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

require_once (dirname(__FILE__) . "/include/config.php");
require_once (dirname(__FILE__) . "/lib/util.php");
require_once (dirname(__FILE__) . "/lib/coduser.php");

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

?>

<section style="margin-bottom: 20px; text-align: justify;">
    <h1>Inicio</h1>
    <article>
        <h3>Resumen general</h3>
        <div class="chart_wrapper">
            <h4>Zonas por servidor</h4>
            <br>
            <canvas id="zone_usage" width="150" height="100"></canvas>
            <div id="zone_usage_legend"></div>
            <script type="text/javascript">
                var zone_usage = document.getElementById("zone_usage").getContext('2d');;
                var zonedata   = [

    <?php
        $dbclient = new DBClient($db_config);
        $dbclient->connect() or die($dbclient->lq_error());
        $q = "select s.id,s.tag,(select count(*) from zones z where z.server_id=s.id ) as nzones from servers s;";
        $results = $dbclient->exeq($q) or die ($dbclient->lq_error());

        while ($r = $dbclient->fetch_object($results)) {
            ?>
                {
                    value: <?php echo $r->nzones; ?>,
                    label: "<?php echo $r->tag;?>",
                    color: getNextColor()
                },
        <?php
        }

        $dbclient->disconnect();
    ?>
                ];
                var zone_usage_chart = new Chart(zone_usage).Pie(zonedata,{
                    responsive : false,
                    animationEasing: "easeOutQuart",
                    animationSteps : 40,
                    legendTemplate : "<ul class=\"zone_usage-legend\">"
                    + "<% for (var i=0; i<segments.length; i++){%>"
                        +"<li style=\""
                        + "padding: 0;"
                        +"\"><span style=\""
                            + "background-color:<%=segments[i].fillColor%>;"
                            + "width: 1em;"
                            + "height: 1em;"
                            + "display: inline-block;"
                            + "margin: -3px 15px;"
                        + "\"></span>"
                        +"<%if(segments[i].label){%><%=segments[i].label%><%}%>"
                    +"</li><%}%></ul>"
                  });
                var segments = zone_usage_chart.segments;
                document.getElementById("zone_usage_legend").innerHTML = zone_usage_chart.generateLegend();

            </script>
        </div>

    </article>
    <article class="nav">
        <h3>Navegaci&oacute;n r&aacute;pida</h3>

        <div class="mtr_like">
            <a href="<?php echo $config["html_root"] . "/?m=usr&z=hosts"; ?>">Gestionar hosts</a>
            <a href="<?php echo $config["html_root"] . "/?m=adm&z=service"; ?>">Admininistrar el servicio</a>
            <a href="<?php echo $config["html_root"] . "/?m=adm&z=site"; ?>">Admininistrar el sitio</a>
            
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
    <form id="loginf" method="POST" action="usr/rq_login.php" onsubmit="fsgo('loginf', 'login_response','usr/users_rq_login.php', true);return false;">
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
?>
</body>
</html>
