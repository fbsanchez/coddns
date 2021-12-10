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

require_once __DIR__ . "/../lib/db.php";
require_once __DIR__ . "/../include/functions_ip.php";

// Defines CODUser class

class SSHClient
{
    public $ip   = null;
    public $user = null;
    public $pass = null;
    public $port = 22;


    public $server = null;

    public $connection    = false;
    public $authenticated = false;
    public $connected     = false;

    public $stream      = null;
    public $errorStream = null;

    // Builder
    public function __construct($ssh_config)
    {
        $this->load_cfg();

        if ((!isset($ssh_config->ip))
            || (!isset($ssh_config->user))
            || (!isset($ssh_config->pass))
        ) {
            return null;
        }

        $this->ip   = $ssh_config->ip;
        $this->user = $ssh_config->user;
        $this->pass = $ssh_config->pass;
        if (isset($ssh_config->port)) {
            $this->port = $ssh_config->port;
        }

        return $this;
    }

    public function load_cfg()
    {
        include __DIR__ . "/../include/config.php";
        $this->config = $config;
    }

    public function is_connected()
    {
        return $this->connected;
    }

    public function is_authenticated()
    {
        return $this->authenticated;
    }

    /**
     * This public function only connects to remote IP:port,
     * The user must authenticate after check fingerprint.
     * Native adaptation of the ssh library for php
     */
    public function _connect()
    {
        if ($this->connected) {
            return $this->connected;
        }
        $this->connection = ssh2_connect($this->ip, $this->port);
        if ($this->connection != false) {
            $this->connected = true;
        }
        return $this->connection;
    }


    public function authenticate()
    {
        if ($this->connected === false) {
            return false;
        }
        if ($this->connection === false) {
            $this->connected = false;
            return false;
        }

        $this->authenticated = ssh2_auth_password($this->connection, $this->user, $this->pass);

        return $this->authenticated;
    }

    /**
     * Provides a valid connection with an active session
     *
     * @return boolean true  connected & authenticated
     *                 false any other possibility
     */
    public function connect()
    {
        if (!$this->connected) {
            $this->_connect();
        }
        if (!$this->authenticated) {
            $this->authenticate();
        }
        if (!$this->authenticated) {
            return false;
        }
        return true;
    }

    public function exec($command)
    {
        if ($this->connected === false) {
            return null;
        }
        if ($this->connection === false) {
            $this->connected = false;
            return null;
        }
        if ($this->authenticated === false) {
            die("You should authenticate first.");
            return null;
        }

        $this->stream      = ssh2_exec($this->connection, $command);
        $this->errorStream = ssh2_fetch_stream($this->stream, SSH2_STREAM_STDERR);

        $return["stdout"] = $this->stream;
        $return["stderr"] = $this->errorStream;

        return $return;
    }

    public function get_output()
    {
        return stream_get_contents($this->stream);
    }
    public function get_stderr()
    {
        return stream_get_contents($this->errorStream);
    }

    public function disconnect()
    {
        if ($this->connected === false) {
            return null;
        }
        if ($this->connection === false) {
            $this->connected = false;
            return null;
        }
        if ($this->authenticated === false) {
            die("You should authenticate first.");
            return null;
        }
        ssh2_exec($this->connection, 'exit');
        unset($this->connection);

        $this->connected     = false;
        $this->connection    = false;
        $this->authenticated = false;
    }

    public function launch($command)
    {
        if (!$this->connected) {
            $this->_connect();
        }
        if (!$this->authenticated) {
            $this->authenticate();
        }
        if (!$this->authenticated) {
            return null;
        }

        $this->exec($command);
        // Enable blocking for both streams
        stream_set_blocking($this->errorStream, true);
        stream_set_blocking($this->stream, true);

        $out[0] = rtrim($this->get_output(), "\n\r");
        $out[1] = rtrim($this->get_stderr(), "\n\r");

        fclose($this->errorStream);
        fclose($this->stream);

        $this->disconnect();
        return $out;
    }

    public function send_file($local_file, $remote_file)
    {
        if (!$this->connected) {
            $this->_connect();
        }
        if (!$this->authenticated) {
            $this->authenticate();
        }
        if (!$this->authenticated) {
            return null;
        }

        $r = ssh2_scp_send($this->connection, $local_file, $remote_file);

        $this->disconnect();
        return $r;
    }

    public function apply_conf($tmp_file, $running_file)
    {
        if (!$this->connected) {
            $this->_connect();
        }
        if (!$this->authenticated) {
            $this->authenticate();
        }
        if (!$this->authenticated) {
            return null;
        }

        $r = $this->launch("mv -f $tmp_file $running_file");

        $this->disconnect();
        if (($r[0] == "") && ($r[1] == "")) {
            return true;
        }
        return $r;
    }

    public function check_valid_conf($remote_file)
    {
        if (!$this->connected) {
            $this->_connect();
        }
        if (!$this->authenticated) {
            $this->authenticate();
        }
        if (!$this->authenticated) {
            return null;
        }

        $r = $this->launch("named-checkconf $remote_file");

        $this->disconnect();
        return $r;
    }

    public function get_file($remote_file, $local_file)
    {
        if (!$this->connected) {
            $this->_connect();
        }
        if (!$this->authenticated) {
            $this->authenticate();
        }
        if (!$this->authenticated) {
            return null;
        }

        $r = ssh2_scp_recv($this->connection, $remote_file, $local_file);

        $this->disconnect();
        return $r;
    }

    public function set_server_info($server)
    {
        $this->server = $server;
    }

    public function get_server_info()
    {
        return $this->server;
    }
}
