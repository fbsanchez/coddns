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

// CODDNS INSTALLER
defined("_VALID_INCLUDE") or define("_VALID_INCLUDE", 1);
include(__DIR__ . "/include/constants.php");

session_start();

require_once(__DIR__ . "/lib/db.php");

function check_lib($item)
{
    if (!extension_loaded($item)) {
        return false;
    }
    return true;
}

function check_item($item)
{
    if ($item == 1) {
        echo "ok";
    } else {
        echo "fail";
    }
}

function print_header($phase)
{
    ?>
    <header>
        <img src="rs/img/coddns_225.png" alt="logo"/>
        <p style="float: right;margin: 17px 1em 0px 0px;color: #fff;font-size: 0.72em;">Step <?php echo $phase;?>/3</p>
    </header>
    <?php
}

?>

<!DOCTYPE HTML>
<hmtl>
<head>
<title>CODDNS Installer - Integrated management of DNS services</title>

<link rel="icon" href="rs/img/coddns.ico">
<link rel="stylesheet" type="text/css" href="rs/css/pc/install.css">
<script type="text/javascript">
function toggle(id){
    if (id.style['max-height'] == '') {
        id.style['max-height'] = '1000px';
    }
    else {
        id.style['max-height'] = '';
    }
}
function update_data_form(){
    if (engine.value==""){
        data_form.style['max-height'] = '0px';
        return false;
    }
    else if (engine.value=="mysql") {
        schema.style['max-height']='0px';
        schema.style['padding']='0px';
        dbp.value=3306;
    }
    else if (engine.value=="postgresql") {
        schema.style['padding']='2px';
        toggle(schema);
        dbp.value=5432;
    }
    data_form.style['max-height'] = '1000px';
}
</script>
</head>

<body>
<section id="main_wizard">

<?php

if ((!isset($_POST["engine"]))
    || (!isset($_POST["dbroot"]))
    || (!isset($_POST["dbrpass"]))
    || (!isset($_POST["dbuser"]))
    || (!isset($_POST["dbpass"]))
    || (!isset($_POST["dbhost"]))
    || (!isset($_POST["dbport"]))
    || (!isset($_POST["dbname"]))
    || ( strlen($_POST["dbroot"]) < MIN_USER_LENGTH)
    || ( strlen($_POST["dbname"]) < MIN_DB_LENGTH )
    ) { // NO PHASE 2 expected values received, can be at 1 or 3
    $phase = 3;
    if ((!isset($_POST["html_root"]))
        || (!isset($_POST["user"]))
        || (!isset($_POST["pass"]))
        || (!isset($_POST["hash"]))
    ) { // NO PHASE 3 expected values received, I must be on 1
        $phase = 1;
    }
} else { // PHASE 2 expected values received: I should be on 2
    $phase = 2;
}

print_header($phase);

if ($phase == 1) {
// TESTS BEGIN

// First of all unset all active sessions
    session_destroy();
    session_write_close();


    $named_ok  = 0;
    $dnsmgr_ok = 0;
    $mysqli_ok = 0;
    $pgsql_ok  = 0;
    $nmap_ok   = 0;
    $global_ok = 0;
    $writable_config_ok = 0;

// check named service:
    exec("rndc 2>&1 | tail | grep Version", $service_output, $return);
    if ($return == 0) {
        $named_ok  = 1;
    } else {
        $service_output[0] = "Not found.";
    }

// check ddns_manager is present
    exec("which dnsmgr | wc -l", $dnsmgr_output, $return);
    if (($return == 0) && ($dnsmgr_output[0] >= 1)) {
        $dnsmgr_ok  = 1;
    }

// check nmap is present
    exec("which nmap | wc -l", $nmap_output, $return);
    if (($return == 0) && ($nmap_output[0] >= 1)) {
        $nmap_ok  = 1;
    }

// Check if configuration directory is writable
    if (is_writable(__DIR__ . "/include")) {
        $writable_config_ok = 1;
    }

// check php extensions
    if (check_lib("mysqli")) {
        $mysqli_ok = 1;
    }
    if (check_lib("pgsql")) {
        $pgsql_ok  = 1;
    }
    if (check_lib("ssh2")) {
        $ssh2_ok   = 1;
    }

    if ($ssh2_ok+$writable_config_ok == 2) {
        if ($mysqli_ok+$pgsql_ok >= 1) {
            $global_ok = 1;
        }
    }

// TESTS END

//$service_requeriments = $named_ok + $dnsmgr_ok;
//$global_requeriments = $service_requeriments +
    ?>
    <article>
        <div>
            <h1>CODDNS installer</h1>
            <p>Thank you for choose CODDNS for your DNS management.</p>
            <br />
            <p>Please, before you can continue the installation process, you must verify all requirements are accomplished. Those on <b>bold</b> are mandatory.</p>
        </div>
        <div class="t_label" onclick="toggle(requeriments);">
            <div class="status <?php echo check_item($global_ok);?>">&nbsp;</div>Requirements
        </div>
        <div class="tab" id="requeriments" <?php if ($global_ok !=1) {
            echo "style='max-height:1000px;'";
                                           }?> >
            <i>Web portal</i>
            <ul>
                <li>
                    <div class="status ok">&nbsp;</div> <b>Web server</b>
                </li>
                <li>
                    <div class="status ok">&nbsp;</div> <b>PHP</b>
                </li>
                <li>
                    <div class="status <?php check_item($writable_config_ok);?>">&nbsp;</div>
                        <b>Writable configuration directory</b>
                </li>
                <li>
                    <div class="status <?php check_item($ssh2_ok);?>">&nbsp;</div> <b>PHP PECL SSH2</b>
                    <?php if ($ssh2_ok != 1) {?>
                    <br><span style="font-size: 0.9em;margin-left: 25px;">SSH library (centos): yum install php-pecl-ssh2</span>
                    <br><span style="font-size: 0.9em;margin-left: 25px;">SSH library (debian): apt-get install php5-ssh2</span>
                    <?php }?>
                </li>
            </ul>
            <b><i>Database connector</i></b> <span style="font-size:0.65em;">(at least one)</span>
            <ul>
                <li>
                    <div class="status <?php check_item($mysqli_ok);?>">&nbsp;</div> PHP MySQLi
                </li>
                <li>
                    <div class="status <?php check_item($pgsql_ok);?>">&nbsp;</div> PHP PostgreSQL (&gt; 9.X)
                </li>
            </ul>
            <i>Local DNS management</i> <span style="font-size:0.65em;">(optional)</span>
            <ul>
                <li>
                    <div class="status <?php check_item($named_ok);?>">&nbsp;</div>
                        Bind - DNS service<br><span style="font-size: 0.9em;margin-left: 25px;"><?php echo $service_output[0];?></span>
                </li>
                <li>
                    <div class="status <?php check_item($dnsmgr_ok);?>">&nbsp;</div>
                        DNS manager script
                </li>
            </ul>
            <i>Tools</i> <span style="font-size:0.65em;">(optional)</span>
            <ul>
                <li>
                    <div class="status <?php check_item($nmap_ok);?>">&nbsp;</div> nmap
                </li>
            </ul>
        </div>
        <?php
            // DO NOT SHOW FORM TO PHASE 2 if gobal checks are not completed
        if ($global_ok == 1) {
            ?>
        <div class="t_label" onclick="toggle(data);">
            <div class="status">&nbsp;</div>Your information
        </div>
        <div class="tab" id="data" <?php if ($global_ok ==1) {
            echo "style='max-height:1000px;'";
                                   }?>>
            <form id="mysql" name="dbdata" method="POST" onsubmit="dbrpass.value=btoa(dbrpass.value);dbpass.value=btoa(dbpass.value);">
                <label>Database engine:</label>
                <select id="engine" name="engine" onchange="update_data_form();">
                    <option value="" selected>Please select</option>
                <?php
                if ($mysqli_ok) {
                    ?>
                    <option value="mysql">MySQL</option>
                    <?php
                }
                if ($pgsql_ok) {
                    ?>
                    <option value="postgresql">PostgreSQL</option>
                    <?php
                }
                ?>
                </select>
                <ul id="data_form" style="max-height: 0px;overflow:hidden;transition:max-height 1s 0s;">
                <script type="text/javascript">
                function check_dbhost(val){
                    if(   (val.value == "localhost")
                        ||(val.value == "127.0.0.1")) {
                        myip_li.style["display"] = "none";
                        myip.required = false;
                    }
                    else {
                        myip_li.style["display"] = "block";
                        myip.required = true;
                    }
                }
                </script>
                    <li>
                        <label>DB server:</label><input name="dbhost" type="text" value="localhost" onchange="check_dbhost(this);"/>
                    </li>
                    <li id="myip_li" style="display: none;">
                        <label>Source IP:</label><input id="myip" name="myip" type="text"/>
                    </li>
                    <li>
                        <label>Port:</label><input id="dbp" name="dbport" type="number" value="3306"/>
                    </li>
                    <li>
                        <label>DB name:</label><input name="dbname" type="text" value="coddns"/>
                    </li>
                    <li id="schema" style="padding:0;max-height:0;overflow:hidden;">
                        <label>Schema:</label><input name="schema" type="text" value="ddnsp" required/>
                    </li>
                    <li>
                        <label>User:</label><input name="dbroot" type="text" value="root"/>
                    </li>
                    <li>
                        <label>Password:</label><input id="dbrpass" name="dbrpass" type="password"/>
                    </li>
                    <li>
                        <label>New user:</label><input name="dbuser" type="text" value="coddns"/>
                    </li>
                    <li>
                        <label>New password:</label><input id="dbpass" name="dbpass" type="password"/>
                    </li>
                    <li>
                        <label>Perform clean install:</label><input name="dbdrop" type="checkbox" checked/>
                    </li>
                    <li>
                        <input type="submit" value="Install"/>
                    </li>
                </ul>
            </form>
        </div>
            <?php
        }
        ?>
    </article>
    <?php
} elseif ($phase == 2) {
    $engine  = DBClient::prepare($_POST["engine"], "insecure_text");
    $dbroot  = DBClient::prepare($_POST["dbroot"], "insecure_text");
    $dbrpass = base64_decode($_POST["dbrpass"]);
    $dbuser  = DBClient::prepare($_POST["dbuser"], "insecure_text");
    $dbpass  = base64_decode($_POST["dbpass"]);
    $dbname  = DBClient::prepare($_POST["dbname"], "insecure_text");
    $dbhost  = DBClient::prepare($_POST["dbhost"], "insecure_text");
    $dbport  = DBClient::prepare($_POST["dbport"], "number");
    $schema  = DBClient::prepare($_POST["schema"], "insecure_text");
    $dbdrop  = $_POST["dbdrop"];

    // remove spaces from dbname
    $dbname = preg_replace('/\s+/', '', $dbname);
    // and from schema
    $schema = preg_replace('/\s+/', '', $schema);

    if ($engine == "mysql") {
        // Ignore schema in MySQL installation
        unset($schema);
    }


    // if no dbuser is provided, use dbroot as well
    if ("$dbuser" == "") {
        $dbuser = $dbroot;
    }


    // Create temp configuration
    /*
     * Database configuration ~ Temporary based on root user
     */
    $db_config = array("engine"  =>"$engine", // Could be mysql or postgresql
                       "username"=>"$dbroot",
                       "password"=>"$dbrpass",
                       "hostname"=>"$dbhost",
                       "port"    =>"$dbport",
                       "name"    =>"",
                       "schema"  =>"");

    switch ($engine) {
        case "mysql":
            $sql_file = __DIR__ . "/coddns_sql/coddns_mysql.sql";
            break;
        case "postgresql":
            $sql_file = __DIR__ . "/coddns_sql/coddns_pgsql.sql";
            break;
        default:
            $sql_file = "";
            die("Error, please follow the wizard.");
            break;
    }

    file_exists($sql_file) or die("SQL database scripts don't found.");
    

    // Initialize flags
    $sql_connection_ok  = 0;
    $sql_process_ok     = 0;
    $drop_database_ok   = 0;
    $create_database_ok = 0;
    $grant_user_ok      = 0;

    // Connect to SQL
    $dbclient = new DBClient($db_config);

    if ($dbclient->connect()) {
        $sql_connection_ok = 1;
    } else {
        $connection_message = "Couldn't connect to DB server. " . $dbclient->lq_error();
    }
    if ($sql_connection_ok == 1) {
        $engine  = $dbclient->prepare($_POST["engine"], "letters");
        $dbroot  = $dbclient->prepare($_POST["dbroot"], "letters");
        $dbrpass = base64_decode($_POST["dbrpass"]);
        $dbuser  = $dbclient->prepare($_POST["dbuser"], "text");
        $dbpass  = base64_decode($_POST["dbpass"]);
        $dbname  = $dbclient->prepare($_POST["dbname"], "text");
        $dbhost  = strtolower($dbclient->prepare($_POST["dbhost"], "url_get"));
        $myip    = strtolower($dbclient->prepare($_POST["myip"], "text"));
        $dbport  = $dbclient->prepare($_POST["dbport"], "number");
        $schema  = $dbclient->prepare($_POST["schema"], "text");
        $dbdrop  = $_POST["dbdrop"];

        if (!isset($dbport)) {
            switch ($engine) {
                case "mysql":
                    $dbport=3306;
                    break;
                case "postgresql":
                    $dbport=5432;
                    break;
                default:
                    die("Please use the wizard.");
                break;
            }
        }

        if ((!isset($myip))
            || ("$myip" == "")) {
            if (($dbhost == "127.0.0.1")
                || ($dbhost == "localhost")) {
                $myip = $dbhost;
            } else {
                $myip = $_SERVER["SERVER_ADDR"];
            }
        }

        // DROP DATABASE
        if ("$dbdrop" == "on") {
            $q = "drop database if exists $dbname;";
            $r = $dbclient->exeq($q);
            if ($r) {
                $drop_database_ok = 1;
                if ($engine == "postgresql") {
                    $q = "DROP SCHEMA if exists $schema CASCADE;";
                    $q .= "drop role if exists $dbuser;\n";
                    $r = $dbclient->exeq($q);
                    $drop_message = $dbclient->lq_error();
                }
            } else {
                $drop_message = $dbclient->lq_error();
            }
        }

        // CREATE NEW DATABASE ~ Expected it doesn't exist
        $q = "create database $dbname;";
        $r = $dbclient->exeq($q);
        if ($r) {
            $create_database_ok = 1;
        } else {
            $create_message = $dbclient->lq_error();
        }

        if ($create_database_ok == 1) {
            if ($engine == "postgresql") {
                // Grant all privileges on just created database
                $q  = "create user $dbuser;\n";
                $q .= "grant all on database $dbname to $dbuser;\n";
                $q .= "alter user $dbuser with password '$dbpass';\n";
                if ($schema != "") {
                    $q .= "alter user $dbuser set search_path to $schema;\n";
                }
                $r = $dbclient->exeq($q);
                if ($r) {
                    $grant_user_ok = 1;
                } else {
                    $grant_message = $dbclient->lq_error();
                }

                if ($schema != "") {
                    $db_config = array("engine"  =>"$engine", // Could be mysql or postgresql
                       "username"=>"$dbuser",
                       "password"=>"$dbpass",
                       "hostname"=>"$dbhost",
                       "port"    =>"$dbport",
                       "name"    =>"$dbname",
                       "schema"  =>"$schema");
                    $dbclient->disconnect();
                    $dbclient = new DBClient($db_config);
                    $dbclient->connect();
                    $q = "CREATE SCHEMA $schema;";
                    $r = $dbclient->exeq($q);
                    if (!isset($r)) {
                        $create_database_ok = 0;
                        $create_message .= "\n" . $dbclient->lq_error();
                    }
                }
            }
        }

        // PROCESS SQL SCRIPT
        if ($create_database_ok == 1) {
            switch ($engine) {
                case "mysql":
                    if ("$dbrpass" == "") {
                        $command = "mysql -u $dbroot -h $dbhost -P $dbport $dbname < $sql_file";
                    } else {
                        $command = "mysql -u $dbroot -p'$dbrpass' -h $dbhost -P $dbport $dbname < $sql_file";
                    }
                    exec($command . " 2>&1", $sql_file_exec, $return);
                    break;
                case "postgresql":
                    putenv("PGPASSWORD=$dbrpass");

                    $command = "psql -U $dbuser -w -h $dbhost -p $dbport -d $dbname -f $sql_file";
                    exec($command . " 2>&1", $sql_file_exec, $return);
    
                    break;
                default:
                    die("Error, please follow the wizard.");
                break;
            }

            if ($return == 0) {
                $sql_process_ok = 1;
            } else {
                if (!isset($sql_script_message)) {
                    $sql_script_message = "";
                }
                foreach ($sql_file_exec as $line) {
                    $sql_script_message .= $line;
                }
            }
        }
        // GRANT PRIVILEGES MySQL and others but no postgresql
        if (($sql_process_ok == 1) && ($engine != "postgresql")) {
            if ($dbuser == $dbroot) {
                // dbroot user also has grants ~ user password ignored.
                $grant_user_ok = 1;
            } else {
                switch ($engine) {
                    case "mysql":
                        $q = "grant all privileges on $dbname.* to $dbuser@" . $myip . " identified by '$dbpass';";
                        break;
                    default:
                        die("Error, please follow the wizard.");
                    break;
                }

                $r = $dbclient->exeq($q);
                if ($r) {
                    $grant_user_ok = 1;
                } else {
                    $grant_message = $dbclient->lq_error();
                }
            }
        }
        $dbclient->disconnect();

        if ($grant_user_ok == 1) {
            // Succesfully installed, store config to SESSION
            $db_config = array("engine"  =>"$engine", // Could be mysql or postgresql
                   "username"=>"$dbuser",
                   "password"=>"$dbpass",
                   "hostname"=>"$dbhost",
                   "port"    =>"$dbport",
                   "name"    =>"$dbname",
                   "schema"  =>"$schema");
            $_SESSION["config"] = $db_config;
            session_write_close();
        }
    }

    ?>
    <article>
        <div>
            <p>Installation steps:</p>
            
            <br />
            <ul style="width: auto;">
                <li>
                    <div class="status <?php check_item($sql_connection_ok);?>">&nbsp;</div> Connect to DB server
            <?php
            if (isset($connection_message)) {
                echo "<div onclick='toggle(this);' class='err'>Error: $connection_message</div>";
            }
            ?>
                </li>
            <?php
            if ("$dbdrop" == "on") {
                ?>
                <li>
                    <div class="status <?php check_item($drop_database_ok);?>">&nbsp;</div> Clear previous DB: <?php echo $dbname;?>
                <?php
                if (isset($drop_message)) {
                    echo "<div onclick='toggle(this);' class='err'>Error: $drop_message</div>";
                }
                ?>
                </li>
                <?php
            }
            ?>
                <li>
                    <div class="status <?php check_item($create_database_ok);?>">&nbsp;</div> Create new DB: <?php echo $dbname;?>
            <?php
            if (isset($create_message)) {
                echo "<div onclick='toggle(this);' class='err'>Error: $create_message</div>";
            }
            ?>
                </li>
                <li>
                    <div class="status <?php check_item($sql_process_ok);?>">&nbsp;</div> Process deployment scripts
            <?php
            if (isset($sql_script_message)) {
                echo "<div onclick='toggle(this);' class='err'>Error: ";
                echo "<pre>";
                var_dump($sql_script_message);
                echo "</pre>";
                echo "</div>";
            }
            ?>
                </li>
                <li>
                    <div class="status <?php check_item($grant_user_ok);?>">&nbsp;</div> Fix grants to <?php echo $dbuser; ?>
            <?php
            if (isset($grant_message)) {
                echo "<div onclick='toggle(this);' class='err'>Error: $grant_message</div>";
            }
            ?>
                </li>
            </ul>
        </div>
        <?php
        if ($sql_process_ok == 1) {
            /* FORM STEP 3 */
            ?>


        <div class="t_label" onclick="toggle(data);">
            <div class="status">&nbsp;</div>Final installation steps:
        </div>
        <div class="tab" style="max-height: 1000px" id="data">
            <form id="mysql" name="finalcfg" method="POST" onsubmit="pass.value=btoa(pass.value);">
                <input type="hidden" name="myip" value="<?php echo $myip;?>"/>
                <ul>
                    <li>
                        <label>Main HTML directory</label><input name="html_root" type="text" value="<?php echo preg_replace("/\/install\.php$/", "", $_SERVER['REQUEST_URI']);?>"/>
                    </li>
                    <li>
                        <label>Administrator account:</label><input name="user" type="email" required="yes" />
                    </li>
                    <li>
                        <label>Password:</label><input id="pass" name="pass" type="password" required="yes"/>
                    </li>
                    <li>
                        <label>Hash seed:</label><input name="hash" type="text" value="<?php echo base64_encode(time());?>"/>
                    </li>
                    <li>
                        <input type="submit" value="Finish installation"/>
                    </li>
                </ul>
            </form>
        </div>


            <?php
        }
        ?>
    </article>
    <?php
} elseif ($phase == 3) {
    $config = $_SESSION["config"];
    session_write_close();

    $dbclient  = new DBClient($config);
    $dbclient->connect() or die($dbclient->lq_error());

    // store dbh for future connections
    $config["dbh"] = $dbclient;

    $html_root = $_POST["html_root"];
    $user      = $_POST["user"];
    $rq_pass   = base64_decode($_POST["pass"]);
    $salt      = $_POST["hash"];
    $myip      = $dbclient->prepare($_POST["myip"], "ip");

    if (!isset($myip) || ($myip == "")) {
        $myip = $dbclient->prepare("127.0.0.1", "ip");
    }
/*
    // Add current server to database
    $q = "insert into servers (ip) values ('" . $myip . "')";
    $dbclient->exeq($q) or die ($dbclient->lq_error());
    $server_id = $dbclient->last_id();

    // Add current domain, to the zones table
    $q = "insert into zones (domain,gid) values ('" . $domain . "', default)";
    $dbclient->exeq($q) or die ($dbclient->lq_error());
    $zone_id = $dbclient->last_id();

    // TODO XXX: Also create zone in bind server (local) if available
    // TODO2 XXX: Maybe we should ask for server credentials here...


    // Add zone <-> server relation
    $q = "insert into zone_server (id_zone,id_server,id_master) values ($zone_id,$server_id,$server_id)";
    $dbclient->exeq($q) or die ($dbclient->lq_error());

    $dbclient->disconnect();
*/
    // BUILD FINAL CONFIGURATION FILE
    $str_config  = "<?php\n";
    $str_config .= "\n";
    $str_config .= "/*\n";
    $str_config .= " * Database configuration\n";
    $str_config .= " */\n";
    $str_config .= "\$db_config = array(\"engine\"   =>\"" . $config["engine"]   . "\", // Could be mysql or postgresql\n";
    $str_config .= "                    \"username\" =>\"" . $config["username"] . "\",\n";
    $str_config .= "                    \"password\" =>'"  . $config["password"] . "',\n";
    $str_config .= "                    \"hostname\" =>\"" . $config["hostname"] . "\",\n";
    $str_config .= "                    \"port\"     =>\"" . $config["port"]     . "\",\n";
    $str_config .= "                    \"name\"     =>\"" . $config["name"]     . "\",\n";
    $str_config .= "                    \"schema\"   =>\"" . $config["schema"]   . "\");\n";
    $str_config .= "\n";
    $str_config .= "// domain name: FQDN base for the system\n";
    $str_config .= "// html_root: if you want to access http://yousite.yourdomain/coddns\n";
    $str_config .= "//            set it to /coddns, is the nav location\n";
    $str_config .= "\$config = array (\"html_root\"  => \"" . $html_root . "\",\n";
    $str_config .= "                  \"salt\"       => \"" . $salt . "\",\n";
    $str_config .= "                  \"db_config\"  => \$db_config);\n";
    $str_config .= "\n";
    $str_config .= "include_once (__DIR__ . \"/../include/functions_util.php\");\n";
    $str_config .= "\$config = load_extra_config(\$config);\n";
    $str_config .= "\n";
    $str_config .= "?>\n";

    $file = __DIR__ . "/include/config.php";

    if (! is_writable(__DIR__ . "/include")) {
        die("Directory " . __DIR__ . "/include" . " is not writable by the installer, please check requirements.");
    }
    file_put_contents($file, $str_config);


    // FINAL STEP - create admin user using the configuration file
    include_once(__DIR__ . "/include/config.php");
    require_once(__DIR__ . "/include/functions_ip.php");

    $dbclient = new DBClient($db_config);

    $user = $dbclient->prepare($_POST["user"], "email");
    $pass = hash("sha512", $salt . $rq_pass);

    $dbclient->connect() or die("<div onclick='toggle(this);' class='err'>Error: " . $dbclient->lq_error() . "</div>");

    $q = "Select * from users where lower(mail)=lower('" . $user . "');";
    $dbclient->exeq($q) or die("<div onclick='toggle(this);' class='err'>Error: " . $dbclient->lq_error() . "</div>");
    if ($dbclient->lq_nresults() == 0) { // ADD NEW USER
        // Create administrator user
        $q = "insert into users (mail, pass, ip_last_login, first_login, rol) values (lower('" . $user . "'),'" . $pass . "', " . $dbclient->prepare(_ip(), "ip") . ", now(), (select id from roles where tag='admin'));";
        $dbclient->exeq($q) or die("<div onclick='toggle(this);' class='err'>Error: " . $dbclient->lq_error() . "</div>");
        $oid = $dbclient->last_id();

        // Add user to global group
        $q = "insert into tusers_groups (oid,gid,admin) values ($oid,(select id from `groups` where tag='all'),1);";
        $dbclient->exeq($q) or die("<div onclick='toggle(this);' class='err'>Error: " . $dbclient->lq_error() . "</div>");

        // Welcome mail to user
        $text_sender               = "Coddns";
//      $email_sender              = "noreply@" . $config["domainname"];
        $text_mail_welcome_body    = "Hi!\n\n Thank your for install Coddns,\nYou can access the tool at:\n " . $_SERVER['HTTP_ORIGIN'] . $html_root . "\n\nWe hope you enjoy it.\n\nKind regards,\n\nThe Coddns team.\n";
        $text_mail_welcome_subject = "CODDNS installation completed!";

        $recipient = $user;                    //recipient
        $mail_body = $text_mail_welcome_body;  //mail body
        $subject = $text_mail_welcome_subject; //subject
        $header = "From: " . $text_sender . " <" . $email_sender . ">\r\n"; //optional headerfields
        mail($recipient, $subject, $mail_body, $header); //mail command :)
    } else {
        die("<div onclick='toggle(this);' class='err'>Error: user already defined</div>");
        exit(1);
    }

    $dbclient->disconnect();

    // process login with new user.
    require_once(__DIR__ . "/lib/coduser.php");
    $objUser = new CODUser();
    if ($objUser->login($user, $rq_pass) == null) {
        die("<p>Problem loading user, please rerun the installation process.</p>");
    }
    // CONFIG WRITTEN
    ?>
    <article style="text-align: center;">
    <div style="width: 100px; height: 100px; margin: 15px auto;">
        <img src="<?php echo $html_root . "/rs/img/ok.png"?>" alt="END" />
    </div>
    <p>Installation successfully completed!</p>
    <br>
    <p><b>Access information</b> (over the <i>"password"</i> field to display it):</p>

    <ul style="font-size: 0.8em; width: 50%; margin: 25px auto;text-align: left;">
        <li>User: <?php echo $user;?></li>
        <li onmouseout="pass.style['display']='none';" onmouseover="pass.style['display']='inline-block';">Password: <div id="pass" style="display:none;"><?php echo base64_decode($_POST["pass"]);?></div></li>
    </ul>
    <p>Please click <a href="<?php echo $_SERVER['HTTP_ORIGIN'] . $html_root; ?>">here</a> to access the tool.</p>

    </article>

    <?php
} else {
    die("Please follow the wizard");
}
?>
</section>
</body>

</hmtl>

<?php

session_write_close();

?>
