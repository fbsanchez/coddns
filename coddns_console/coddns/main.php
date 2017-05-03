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

try {
    $auth_level_required = get_required_auth_level('','main','');
    $user = new CODUser();
    $user->check_auth_level($auth_level_required);
}
catch (Exception $e) {
    echo $e->getMessage();
    exit (1);
}

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
<link rel="stylesheet" type="text/css" href='<?php echo $config["html_root"] . "/rs/js/external/c3-0.4.11/c3.min.css";?>'>
<script src='<?php echo $config["html_root"] . "/rs/js/external/d3-3.5.7/d3.min.js";?>'></script>
<script src='<?php echo $config["html_root"] . "/rs/js/external/c3-0.4.11/c3.min.js";?>'></script>
</head>

<body>

<?php
if ($user->get_is_logged()){
    // USER ZONE - OVERALL STATUS VIEW

$dbclient = $config["dbh"];

?>

<section style="margin-bottom: 20px; text-align: justify;">
    <h1>Inicio</h1>
    <article>
        <h3>Resumen general</h3>

        <div id="graph">
        </div>
        <center><h4>queries resulted in authoritative answer</h4></center>

        <script type="text/javascript">

            <?php
            // print as many "serieX" as graphs to show in the same draw area
            ?>

            var serie1 = {status:"",response:"", painted:0};
            var serie2 = {status:"",response:"", painted:0};

            var series = [];
            var restantes=2;

            var chart = c3.generate({
              bindto: '#graph',
              data: {
                xFormat: '%Y-%m-%dT%H:%M:%S',
                xs: {
            <?php
            // Also print X-Y mappping references
            ?>
                  'coddns.org':'t_coddns.org',
                  'senoscasan.net':'t_senoscasan.net'
                },
                columns: []
              },
              axis: {
                  x: {
                      type: 'timeseries',
                      tick: {
                              format: '%Y-%m-%d %H:%M:%S'
                      }
                  }
              }
            });



            function harvest_data() {

            setTimeout(function () {
              var readed = 0;
            <?php
            // Print entire block as many times as graphs to show in the same draw area
            ?>
              if ((serie1) && (serie1.status) && (serie1.status == 200) && (serie1.painted == 0)) {
                chart.load({
                  columns: [
                      JSON.parse(serie1.response)["values"],
                      JSON.parse(serie1.response)["timestamps"]
                  ]
                });
                serie1.painted=1;
                readed++;
              }
            <?php
            // *** END ***
            ?>
              if ((serie2) && (serie2.status) && (serie2.status == 200) && (serie2.painted == 0)) {
                chart.load({
                  columns: [
                      JSON.parse(serie2.response)["values"],
                      JSON.parse(serie2.response)["timestamps"]
                  ]
                });
                serie2.painted=1;
                readed++;
              }

              if (readed >= restantes) {
                return;
              }
              else {
                harvest_data();
              }
            },1000);
            }

            (function () {
            <?php 
            // Generate as many AJAX calls as graphs to show in the same draw area
            ?>
                get_ajax_response("api.php",'action=get_data&args={"oid":396,"custom_tag":"coddns.org"}',serie1);
                get_ajax_response("api.php",'action=get_data&args={"oid":386,"custom_tag":"senoscasan.net"}',serie2);

                harvest_data();
            })();
        </script>






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

?>
</body>
</html>
