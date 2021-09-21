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

require_once __DIR__ . "/include/config.php";
require_once __DIR__ . "/include/functions_util.php";
require_once __DIR__ . "/lib/graphs.php";
require_once __DIR__ . "/lib/coduser.php";

if (! defined("_VALID_ACCESS")) { // Avoid direct access
    header("Location: " . $config["html_root"] . "/");
    exit(1);
}

try {
    $auth_level_required = get_required_auth_level('', 'main', '');
    $user = new CODUser();
    $user->check_auth_level($auth_level_required);
} catch (Exception $e) {
    echo $e->getMessage();
    exit(1);
}

session_start();
if (!isset($_SESSION["lan"])) {
    $_SESSION["lan"] = "es";
}
$lan = $_SESSION["lan"];
session_write_close();

?>

<!DOCTYPE html>

<html>

<head>
<title>Overall page</title>
<link rel="stylesheet" type="text/css" href="<?php echo $config["html_root"] . "/rs/css/" . $config["html_view"]; ?>/main_overall.css">
<link rel="stylesheet" type="text/css" href='<?php echo $config["html_root"] . "/rs/js/external/c3-0.4.11/c3.min.css";?>'>
<script src='<?php echo $config["html_root"] . "/rs/js/external/d3-3.5.7/d3.min.js";?>'></script>
<script src='<?php echo $config["html_root"] . "/rs/js/external/c3-0.4.11/c3.min.js";?>'></script>
</head>

<body>

<?php
if ($user->get_is_logged()) {
    // USER ZONE - OVERALL STATUS VIEW

    $dbclient = $config["dbh"];

    ?>

<section style="margin-bottom: 20px; text-align: justify;">
    <article>
        <h2>News feed</h2>
        <p>Welcome to Coddns main page</p>
        <?php
        if (isset($config["first_steps"]) && ($config["first_steps"] == 1)) {
            // Print step list
            echo "<p>Now you can follow next steps to bring coddns UP and running:</p>";
            ?>
            <ol>
            <li>Connect to an existent DNS server? Go to <a href="<?php echo $config["html_root"];?>/?m=adm">Administration</a> &gt; <a href="<?php echo $config["html_root"];?>/?m=adm&z=center#servers">Server manager</a></li>
            <li>Add zones to a server?  Go to <a href="<?php echo $config["html_root"];?>/?m=adm">Administration</a> &gt; <a href="<?php echo $config["html_root"];?>/?m=adm&z=center#zones">Zone manager</a></li>
            <li>Create new hosts in the zones? Go to <a href="<?php echo $config["html_root"];?>/?m=usr&z=hosts">Host management</a></li>
            </ol>

            <?php
        }
        ?>
        <p>If you have the coddns core running, you'll see here some information about the status of the DNS service.</p>

        <?php
        $options = $dbclient->get_sql_all_objects("select * from stats_item");

        if ($options["nitems"] > 0) {
            ?>
            <h3>DNS statistics</h3>
            <br/><br/>

            <h5 id="loading_charts_msg">Loading charts...</h5>
            <div id="graph">
            </div>
            <center><h5 style="cursor:pointer;" onclick="document.getElementById('chart_selector').style['max-height'] = '350px';">Bind stats</h5></center>



            <div id="chart_selector"><center>
            <select multiple="yes" id="chart_list">
            <?php
            
            $default_selected = $dbclient->get_sql_all_objects("select * From stats_item where tag like \"Name server%in auth%\";");

            foreach ($options["data"] as $option) {
                echo "<option id='" . $option->id . "' value='v" . sha1($option->tag) . "'>" . $option->tag . "</option>";
            }
            ?>
            </select>

            <input type="submit" value="Add to chart" onclick="add_charts(); return false;" />
            <input type="submit" value="Clear all" onclick="clear_charts(); return false;" />

            </center>
            </div>

            <script type="text/javascript">

                function clear_charts() {
                    chart.unload();
                }

                function add_charts(){
                    document.getElementById('loading_charts_msg').style["display"] = 'block';
                    var selected = document.getElementById("chart_list").selectedOptions;
                    for( var i=0; i<selected.length; i++) {
                        //default_objects.push();
                        var obj_name = selected[i].value;
                        queue[obj_name] = {status:"",response:"", painted:0};
                        queue_handler.push(obj_name);
                        get_ajax_response("api.php",'action=get_data&args={"oid":' + selected[i].id + ',"custom_tag":"' + selected[i].text + '"}', queue[obj_name]);
                        remaining++;
                    };
                    harvest_data();
                }

                var all_items = {
                    <?php
                        // print as many "serieX" as graphs to show in the same draw area
                    foreach ($options["data"] as $option) {
                        echo 'v' . sha1($option->tag) . ':{status:"",response:"", painted:0},';
                    }
                    ?>
                };

                var queue = {};
                var queue_handler = [];
                var remaining=<?php echo $default_selected["nitems"];?>;


                <?php
                /*
                var serie1 = {status:"",response:"", painted:0};
                var serie2 = {status:"",response:"", painted:0};
                */
                ?>            

                var chart = c3.generate({
                  bindto: '#graph',
                  data: {
                    xFormat: '%Y-%m-%dT%H:%M:%S',
                    xs: {
                <?php
                // Also print X-Y mappping references
                /*
                      'coddns.org':'t_coddns.org',
                      'senoscasan.net':'t_senoscasan.net',
                */
                foreach ($options["data"] as $option) {
                    echo "'" . $option->tag . "':'t_" . $option->tag . "',";
                }
                ?>
                    },
                    columns: []
                  },
                  point: {
                    show: false,
                    r: 0,
                  },
                  axis: {
                      x: {
                          type: 'timeseries',
                          tick: {
                                  format: '%Y-%m-%d %H:%M:%S'
                          }
                      }
                  },


    /*
                transition: {
                    duration: 400
                }
                subchart: {
                    show: true
                },
                size: {
                    height: 480
                }
    */
                });


                function load_chart(data) {
                    if ((data != null) && (data.status != null) && (data.status == 200) && (data.painted == 0)) {
                        chart.load({
                          columns: [
                              JSON.parse(data.response)["values"],
                              JSON.parse(data.response)["timestamps"]
                          ],
                          type: 'spline', <?php /* spline, bar, area, stacked-area, pie*/?>
                        });
                        data.painted=1;
                        return 1;
                    }
                    if ((data != null) && (data.painted==1)) {
                        return 1;
                    }
                    return 0;
                }

                function harvest_data() {
                    setTimeout(function () {
                        for (var i = 0; i < queue_handler.length; i++) {
                            item=queue_handler.pop()
                            var ret =load_chart(queue[item]);
                            if(ret > 0) {
                                remaining -= ret;
                            }
                            else {
                                // enqueue again
                                queue_handler.push(item);
                            }

                        }

                        if (remaining <= 0) {
                            document.getElementById('loading_charts_msg').style["display"] = 'none';
                            return;
                        }
                        else {
                            harvest_data();
                        }
                    },1000);
                }

            </script>

            <script type="text/javascript">

                (function () {
                <?php
                // Generate as many AJAX calls as graphs to show in the same draw area
                
                // print as many "serieX" as graphs to show in the same draw area
                foreach ($default_selected["data"] as $option) {
                    $obj_name = "v" . sha1($option->tag);
                    ?>
                queue.<?php echo $obj_name; ?> = {status:"",response:"", painted:0};
                console.log("initialized: [<?php echo $obj_name; ?>]" + queue);
                queue_handler.push("<?php echo $obj_name; ?>");
                    <?php
                    echo "get_ajax_response('api.php','action=get_data&args={\"oid\":" . $option->id . ",\"custom_tag\":\"" . $option->tag . "\"}', queue.v" . sha1($option->tag) . ");\n";
                }

                /*
                    get_ajax_response("api.php",'action=get_data&args={"oid":396,"custom_tag":"coddns.org"}',serie1);
                    get_ajax_response("api.php",'action=get_data&args={"oid":386,"custom_tag":"senoscasan.net"}',serie2);
                */
                ?>
                harvest_data();
                
                })();
            </script>
            <?php
            // close graph view
        }
        ?>





    </article>
<!--
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
-->
</section>

    <?php
} else {
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
    <h2>Welcome to <b>coddns</b></h2>

    <p>Please use your account to access the system.</p>
    <?php
    if (get_required_auth_level('usr', 'users', 'signin') == 0) {
        // SHOW THE SIGNIN FORM IF IT'S ENABLED ON CONFIGURATION
        ?>
    <p>If you haven't an account please use the form provided <a href="<?php echo $config["html_root"] . "/?m=usr&z=users&op=login"?>">here</a> to get one.</p>
        <?php
    }
    ?>
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
