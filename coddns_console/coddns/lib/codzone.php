<?php
/**
 * <copyright company="CODDNS">
 * Copyright (c) 2017 All Right Reserved, http://coddns.es/
 *
 * THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY
 * KIND, EITHER EXPRESSED OR IMPLIED, NO INCLUDING THE WARRANTIES OF
 * MERCHANTABILITY AND/OR FITNESS FOR A PARTICULAR PURPOSE.
 *
 * </copyright>
 * <author>Fco de Borja Sanchez</author>
 * <email>fborja.sanchezs@gmail.com</email>
 * <date>2017-04-21</date>
 * <update>2017-04-21</udate>
 * <summary> </summary>
 */
require_once __DIR__ . "/db.php";
require_once __DIR__ . "/../include/functions_ip.php";
require_once __DIR__ . "/../include/functions_util.php";

// Defines CODZone class

class CODZone
{
    /*
    var $serial; // serial number
    var $ref; // refresh, 2d
    var $ret; // update retry, default 15m
    var $ex;  // expiry, default 2w
    var $nx;  // nx domain ttl, def. 1h
    */
    var $id; // Zone id
    var $file; // File where the zone is defined
    var $domain; // domain ~ tag
    var $gid; // group
    var $status; // status, last unix timestamp since replication
    var $is_public; // flag is public
    var $master_server; // master server where the zone is defined
    var $servers; // array of servers where zone is defined

    function CODZone($data = null)
    {
        if ($data === null) {
            return $this;
        }
        if (isset($data["id"])) {
            $this->id        = $data["id"];
        }
        if (isset($data["file"])) {
            $this->file      = $data["file"];
        }
        if (isset($data["domain"])) {
            $this->domain    = $data["domain"];
        }
        if (isset($data["gid"])) {
            $this->gid       = $data["gid"];
        }
        if (isset($data["status"])) {
            $this->status    = $data["status"];
        }
        if (isset($data["is_public"])) {
            $this->is_public = $data["is_public"];
        }
        if (isset($data["master_server"])) {
            $this->master_server = $data["master_server"];
        }
        if (isset($data["servers"])) {
            $this->servers = $data["servers"];
        } else {
            $this->servers = array();
        }
    }

    /**
     * Has file
     */
    function has_file()
    {
        global $config;

        if (isset($this->file)) {
            if (file_exists($config["spooldir"] . "/" . $this->file)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Save zone
     * creating if not exists in db, updating it if is already defined
     */
    function save()
    {
        global $config;
        $dbh = $config["dbh"];

        if (!isset($this->id)) {
            // Create new entry

            // No ID set, create a new entry in DB
            // 1. search for duplicates
            $r = $dbh->do_sql('SELECT count(*) from zones where domain="' . $this->domain . '"');
            if ($r->nresults > 0) {
                throw new Exception("Zone already defined in DB.");
                return false;
            }
            // 2. add to DB
            $r = $dbh->do_sql(
                'INSERT INTO zones (domain,gid,config,status,is_public) VALUES ('
                . '"' . $this->domain . '",'
                . $this->gid . ","
                . '"' . $this->config . '",'
                . $this->status . ','
                . ($this->is_public?"1":"0")
                . ')'
            );

            $this->id = $dbh->last_id();
            if ($this->id === null) {
                   // Failed to add registry
                   return false;
            }
        } else {
            // Update existing registry
            // 1. search for duplicates
            $r = $dbh->do_sql('SELECT count(*) from zones where domain="' . $this->domain . '"');
            if ($r->nresults == 0) {
                throw new Exception("Zone not found in DB.");
                return false;
            }
            // 2. add to DB
            $r = $dbh->do_sql(
                'UPDATE zones SET '
                . 'domain="' . $this->domain . '",'
                . 'gid=' . $this->gid . ","
                . 'config="' . $this->config . '",'
                . 'status=' . $this->status . ','
                . 'is_public=' . ($this->is_public?"1":"0")
                . ' WHERE id=' . $this->id
            );
        }
    }

    /**
     * Retrieve master server
     */
    function get_master_server()
    {
        return $this->master_server;
    }

    /**
     * Mark server id as master server
     */
    function set_master_server($server_id)
    {
        global $config;
        $dbh = $config["dbh"];

        $sid = $dbh->prepare($server_id, "number");
        if (!isset($sid)) {
            return false;
        }

        // Update db reference
        $r = $dbh->do_sql("UPDATE zones SET master_server=" . $sid . " where id=" . $this->id);
        if ($r->nresults > 0) {
            // Successfully updated
            // Update target as master
            $this->master_server = $sid;
            return true;
        }

        return false;
    }

    
    /**
     * Add target server to server list
     */
    function add_server_reference($server_id)
    {
        global $config;
        $dbh = $config["dbh"];

        $sid = $dbh->prepare($server_id, "number");
        if (!isset($sid)) {
            return false;
        }

        if (!in_array($sid, $this->servers)) {
            // add reference in DB

            // Add connection
            $r = $dbh->get_sql_array('SELECT * from zone_server where id_zone=' . $this->id . ' and id_server=' . $sid);

            array_push($this->servers, $sid);

            return true;
        }

        return false;
    }
}
