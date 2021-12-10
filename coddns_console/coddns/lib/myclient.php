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

class MyClient
{
    public $username;
    public $password;
    public $hostname;
    public $schema;
    public $port;
    public $db;
    public $link = null;
    public $last_query = null;
    public $nresutls = null;
    public $error = null;

    public function __construct($db_config)
    {

        $this->username = $db_config["username"];
        $this->password = $db_config["password"];
        $this->hostname = $db_config["hostname"];
        $this->port     = $db_config["port"];
        $this->db       = $db_config["name"];
        $this->schema   = $db_config["schema"];
    }

    public function connect()
    {
        //    $err_level = error_reporting(0);
        // avoid warnings: Headers and client lib minor version mismatch
        $this->link = mysqli_connect(
            $this->hostname,
            $this->username,
            $this->password,
            $this->db,
            $this->port
        );
        //    error_reporting($err_level);
        if (!$this->link) {
            $this->error =  mysqli_connect_errno() . PHP_EOL;
            return false;
        }
        return true;
    }

    public function is_connected()
    {
        if ($this->link) {
            return mysqli_ping($this->link);
        }
        return false;
    }

    /**
     * Beware of use without prepare the query first
     */
    public function exeq($query)
    {
        $this->last_query = $query;
        $result = mysqli_query($this->link, $query);
        if (!$result) {
            $this->error = mysqli_error($this->link);
            return null;
        }
        if ($result === true) { // UPDATE / DELETE queries
            $this->nresults = mysqli_affected_rows($this->link);
        } else {
            $this->nresults = mysqli_num_rows($result);
        }
        return $result;
    }

    public function lq_error()
    {
        return $this->error;
    }

    public function lq_nresults()
    {
        return $this->nresults;
    }

    public function disconnect()
    {
        mysqli_close($this->link);
    }

    public function fetch_object($result)
    {
        return mysqli_fetch_object($result);
    }
    public function fetch_array($result)
    {
        return mysqli_fetch_array($result);
    }
    public function escape_string($str)
    {
        return mysqli_real_escape_string($this->link, $str);
    }
    public function last_id()
    {
        return mysqli_insert_id($this->link);
    }
}
