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
require_once __DIR__ . "/db.php";
require_once __DIR__ . "/../include/functions_ip.php";
require_once __DIR__ . "/../include/functions_util.php";

// Defines CODServer class

class CODServer
{
    public $ip;
    public $tag;
    public $port;
    public $pass;
    public $user;
    public $status;
    public $config;
    public $gid;
    public $main_config_file;
    public $mastery;
    public $server_load;
    public $tmp_dir;

    /**
     * Initializes a CODServer object based on
     *     the name of the server or hash if is an empty object
     *
     * @param  server_name $server_name
     * @return Int returns the ID of the server (if any)
     */
    public function __construct($raw = false)
    {
        global $config;

        if ((!isset($raw)) || ($raw === false)) {
            return $this;
        } elseif (is_array($raw)) {
            // create new volatile server from hash
            // If saved will be created as a new server! use $raw as name to load from db
            $this->id = null;
            if (isset($raw["ip"])) {
                $this->ip     = _long2ip($raw["ip"]);
            }
            if (isset($raw["gid"])) {
                $this->gid    = $raw["gid"];
            }
            if (isset($raw["tag"])) {
                $this->name   = $config["dbh"]->decode($raw["tag"]);
            }
            if (isset($raw["port"])) {
                $this->port   = $raw["port"];
            }
            if (isset($raw["pass"])) {
                $this->pass   = coddns_decrypt($raw["pass"]);
            }
            if (isset($raw["user"])) {
                $this->user   = $config["dbh"]->decode($raw["user"]);
            }
            if (isset($raw["status"])) {
                $this->status = $raw["status"];
            } else {
                $this->status = 0;
            }
            if (isset($raw["mastery"])) {
                $this->mastery = $raw["mastery"];
            } else {
                $this->mastery = 100;
            }
            if (isset($raw["server_load"])) {
                $this->server_load = $raw["server_load"];
            } else {
                $this->server_load = 0;
            }
            if (isset($raw["main_config_file"])) {
                $this->main_config_file = $config["dbh"]->decode($raw["main_config_file"]);
            }
            if (isset($raw["fingerprint"])) {
                $this->fingerprint = $config["dbh"]->decode($raw["fingerprint"]);
            } else {
                $this->fingerprint = null;
            }
            if (isset($raw["tmp_dir"])) {
                $this->tmp_dir = $raw["tmp_dir"];
            } else {
                $this->tmp_dir = "/tmp";
            }

            return $this;
        }
        
        return $this->load_server($raw);
    }

    public function load_server($server_name)
    {
        global $config;
        
        $dbclient = $config["dbh"];
        
        $secure_name = $dbclient->prepare($server_name, "text");
        $query = 'select * from servers where tag="' . $server_name . '"';
        $r = $dbclient->get_sql_object($query);
        if (isset($r)) {
            $this->ip     = _long2ip($r->ip);
            $this->id     = $r->id;
            $this->gid    = $r->gid;
            $this->name   = $dbclient->decode($r->tag);
            $this->port   = $r->port;
            $this->pass   = coddns_decrypt($r->srv_password);
            $this->user   = $dbclient->decode($r->srv_user);
            $this->status = $r->status;
            $this->main_config_file = $dbclient->decode($r->main_config_file);
            $this->fingerprint      = $dbclient->decode($r->fingerprint);
            $this->mastery          = $r->mastery;
            $this->server_load      = $r->server_load;
            $this->tmp_dir          = $dbclient->decode($r->tmp_dir);
        }
        return $this;
    }

    public function save_all()
    {
        global $config;
        if (!isset($this->name)) {
            return false;
        }

        $dbclient = $config["dbh"];

        // Validate fields
        if ((!isset($this->name))
            || ($this->name == "")
            || (!isset($this->ip))
            || ($this->ip == "")
            || (!isset($this->main_config_file))
            || ($this->main_config_file == "")
        ) {
            return false;
        }


        // CDE ~ this data could have been provided by users
        $tag  = $dbclient->prepare($this->name, "text");
        $user = $dbclient->prepare($this->user, "text");
        $pass = coddns_encrypt($this->pass);
        $ip   = _ip2long($this->ip);
        $port = $dbclient->prepare($this->port, "number");
        $fingerprint      = $dbclient->prepare($this->fingerprint, "text");
        $main_config_file = $dbclient->prepare($this->main_config_file, "text");
        $tmp_dir          = $dbclient->prepare($this->tmp_dir, "text");

        // internal data
        $status      = $this->status;
        $server_load = $this->server_load;
        $mastery     = $this->mastery;




        // create or update data server information
        if (!isset($this->id)) {
            // create new server
            $q = "insert into servers (tag,ip,port,srv_user,srv_password,main_config_file,fingerprint,status,server_load,mastery,tmp_dir) values"
            . "(\"$tag\", \"$ip\", $port, \"$user\", \"$pass\", \"$main_config_file\", \"$fingerprint\", $status, $server_load, $mastery, \"$tmp_dir\")";

            if ($dbclient->do_sql($q)) {
                $this->id = $dbclient->last_id();
                return $this->id;
            }
            return false;
        } else {
            $q = "udpate servers set tag=\"$tag\", ip=\"$ip\", port=$port, srv_user=\"$user\",srv_password=\"$pass\",main_config_file=\"$main_config_file\" "
            . ",fingerprint=\"$fingerprint\",status=$status,server_load=$server_load,mastery=$mastery,tmp_dir=\"$tmp_dir\"";

            if ($dbclient->do_sql($q)) {
                return true;
            }
        }

        return false;
    }

    private function load_cfg()
    {
        if (empty($this->config)) {
            global $config;
            $this->config = $config;
        }
    }

    // Getters
    
    function get_id()
    {
        return $this->id;
    }

    function has_credentials()
    {
        if (isset($this->user) && (isset($this->pass)) && (isset($this->port))) {
            return true;
        }
        return false;
    }

    function set_credentials($user, $pass, $port = null)
    {
        global $config;

        $dbclient = $config["dbh"];

        $this->user = $dbclient->prepare($user, "text");
        $this->pass = $pass;
        $this->port = $dbclient->prepare($port, "number");
    }

    function save_credentials()
    {
        global $config;

        if (empty($this->id)) {
            return false;
        }

        $dbclient = $config["dbh"];
        $secured_user = $dbclient->prepare($this->user, "text");
        $secured_pass = coddns_encrypt($this->pass);
        if (isset($port)) {
            $this->port = $dbclient->prepare($this->port, "number");
        }
        return $dbclient->do_sql(
            "update servers set srv_password = '" . $secured_pass
            . "', srv_user='" . $secured_user
            . "', port='" . $this->port
            . "' where id='" . $this->id . "'"
        );
    }

    function forgot_credentials()
    {
        global $config;

        if (empty($this->id)) {
            return false;
        }

        $dbclient = $config["dbh"];
        $query = 'update servers set srv_password="", srv_user="" where id=' . $this->id;
        $dbclient->do_sql($query);
        unset($this->pass);
    }
}
