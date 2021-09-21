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
 * <date>2016-03-24</date>
 * <update>2016-03-24</udate>
 * <summary> </summary>
 *
 *
 * Class SSHClient is based in SSH2 PHP Library
 * Allows to connect to a remote machine and execute
 * commands.
 *
 *
 * Requires php-pecl-ssh2
 * yum --enablerepo=remi install php-pecl-ssh2
 */

require_once __DIR__ . "/../lib/codserver.php";

function get_server_data($db_config, $servername)
{
    global $config;
    // retrieve credentials from DB

    $dbclient = $config["dbh"];

    $q = "Select * from servers where tag='" . $servername . "' ;";
    $server = $dbclient->get_sql_object($q);

    if (empty($server)) {
        die("No hay servidores registrados con ese nombre [$servername].");
    }


    // tried to get DB data
    $server_info = array();
    $server_info["user"] = $server->srv_user;
    $server_info["pass"] = coddns_decrypt($server->srv_password);

    // also tried to get user specifications (form), if defined.
    session_start();
    if ((isset($_SESSION["servers"][$servername]["user"]))
        && (isset($_SESSION["servers"][$servername]["pass"]))
    ) {
        $server_info = $_SESSION["servers"][$servername];
    }

    if ((! isset($server_info["user"]) ) || (! isset($server_info["pass"]))) {
        return false;
    }

    // SERVER CREDENTIALS ARE SET
    // transform fields
    $server->user = $dbclient->decode($server_info["user"]);
    $server->pass = $server_info["pass"];
    $check        = _long2ip($server->ip);
    $server->main_config_file = $dbclient->decode($server->main_config_file);
    $server->tmp_dir          = $dbclient->decode($server->tmp_dir);


    if ($check !== false) {
        // not a FQDN, IP loaded
        $server->ip = $check;
    }


    return $server;
}



function transfer_conf_files($config, $sshclient, $serverid, $remote_configfile)
{
    // get folder name
    
    $servers_spool = $config["spooldir"] . "/servers/";
    $zones_spool   = $config["spooldir"] . "/zones/";
    // Create if not exists
    if (! is_dir($servers_spool)) {
        mkdir($servers_spool, 0770);
    }
    if (! is_dir($zones_spool)) {
        mkdir($zones_spool, 0770);
    }

    $localfile   = $servers_spool . $serverid . $remote_configfile;
    $localfolder = dirname($localfile);

    // create local folder
    if (!is_dir($localfolder)) {
        mkdir($localfolder, 0770, true);
    }

    // retrieve remote file
    if ($sshclient->get_file($remote_configfile, $localfile)) {
        //error_log("File " . $remote_configfile . " copied to " . $localfolder);
        return $localfile;
    } else {
        error_log("Failed to copy file " . $remote_configfile . " copied to " . $localfolder);
    }
    return null;
}


/**
 * get_server_connection Returns a new sshclient object
 *
 * @return sshclient session or false if process fails
 */
function get_server_connection_from_hash($server)
{
    global $config;

    if (!isset($server)) {
        return false;
    }

    include_once __DIR__ . "/../lib/sshclient.php";

    $sh = new StdClass();
    $sh->user = $server->srv_user;
    $sh->pass = coddns_decrypt($server->srv_password);
    $sh->ip   = _long2ip($server->ip);
    $sh->port = $server->port;

    // initialize ssh client
    $sshclient = new SSHClient($sh);

    if ($sshclient === null) {
        return false;
    }

    $sshclient->set_server_info($server);

    return $sshclient;
}


/**
 * get_server_connection Returns an active sshclient against the target server
 * Be sure to scape the servername before call this function!
 *
 * @return sshclient session or false if process fails
 */
function get_server_connection($servername)
{
    global $config;

    try {
        $auth_level_required = get_required_auth_level('adm', 'server', 'control');
        $user = new CODUser();
        $user->check_auth_level($auth_level_required);
    } catch (Exception $e) {
        echo $e->getMessage();
        exit(1);
    }


    $dbclient = $dbclient = $config["dbh"];
    
    if (!isset($servername)) {
        echo "Unauthorized to access this content.";
        return false;
    }

    include_once __DIR__ . "/../lib/sshclient.php";


    // Retrieve server credentials
    $server = get_server_data($config["db_config"], $servername);

    if ($server === false) {
        echo "No existen credenciales para acceder a este servidor.";

        return 0;
    }


    if (empty($server->tag)) {
        echo "No hay servidores registrados con ese nombre.";
        return 0;
    }

    if (isset($server->main_config_file)) {
        // initialize ssh client
        $sshclient = new SSHClient($server);

        $sshclient->set_server_info($server);

        $sshclient->connect();

        return $sshclient;
    }

    return false;
}


function get_server_name($server_id)
{
    global $config;
    $dbh = $config["dbh"];

    $sid = $dbh->prepare($server_id, "number");

    if (!$sid) {
        return false;
    }

    $r = $dbh->get_sql_object("SELECT tag from servers where id=" . $sid);

    if ($r !== false) {
        return $r->tag;
    }
    
    return false;
}
