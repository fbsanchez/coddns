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

require_once (__DIR__ . "/../lib/db.php");
require_once (__DIR__ . "/../lib/ipv4.php");

// Defines CODUser class

class SSHClient {
	var $ip   = null;
	var $user = null;
	var $pass = null;
	var $port = 22;

	var $connection    = false;
	var $authenticated = false;
	var $connected     = false;

	var $stream      = null;
	var $errorStream = null;

	// Builder
	function SSHClient($ssh_config){
		$this->load_cfg();

		if (   (!isset($ssh_config["ip"]))
			|| (!isset($ssh_config["user"]))
			|| (!isset($ssh_config["pass"])) ){
			return null;
		}

		$this->ip   = $ssh_config["ip"];
		$this->user = $ssh_config["user"];
		$this->pass = $ssh_config["pass"];
		if (isset($ssh_config["port"])){
			$this->port = $ssh_config["port"];
		}

		return $this;

	}

	function load_cfg(){
		include (__DIR__ . "/../include/config.php");
		$this->config = $config;
	}

	function is_connected(){
		return $this->connected;
	}

	function is_authenticated(){
		return $this->authenticated;
	}

	/**
	 * This function only connects to remote IP:port,
	 * The user must authenticate after check fingerprint.
	 * Native adaptation of the ssh library for php
	 */
	function _connect(){
		if ($this->connected){
			return $this->connected;
		}
		$this->connection = ssh2_connect($this->ip, $this->port);
		if ($this->connection != false){
			$this->connected = true;
		}
		return $this->connection;
	}


	function authenticate(){
		if ($this->connected === false){
			return false;
		}
		if ($this->connection === false){
			$this->connected = false;
			return false;
		}

		$this->authenticated = ssh2_auth_password($this->connection, $this->user, $this->pass);

		return $this->authenticated;
	}

	/**
	 * Provides a valid connection with an active session
	 * @return boolean true  connected & authenticated
	 *                 false any other possibility
	 */
	function connect(){
		if (!$this->connected){
			$this->_connect();
		}
		if (!$this->authenticated){
			$this->authenticate();
		}
		if (!$this->authenticated){
			return false;
		}
		return true;
	}

	function exec($command){
		if ($this->connected === false){
			return null;
		}
		if ($this->connection === false){
			$this->connected = false;
			return null;
		}
		if ($this->authenticated === false){
			die ("You should authenticate first.");
			return null;
		}

		$this->stream      = ssh2_exec($this->connection, $command);
		$this->errorStream = ssh2_fetch_stream($this->stream, SSH2_STREAM_STDERR);

		$return["stdout"] = $this->stream;
		$return["stderr"] = $this->errorStream;

		return $return;
	}

	function get_output(){
		return stream_get_contents($this->stream);
	}
	function get_stderr(){
		return stream_get_contents($this->errorStream);
	}

	function disconnect(){
		if ($this->connected === false){
			return null;
		}
		if ($this->connection === false){
			$this->connected = false;
			return null;
		}
		if ($this->authenticated === false){
			die ("You should authenticate first.");
			return null;
		}
		ssh2_exec($this->connection, 'exit');
		unset($this->connection);

		$this->connected     = false;
		$this->connection    = false;
		$this->authenticated = false;
	}

	function launch($command){
		if (!$this->connected){
			$this->_connect();
		}
		if (!$this->authenticated){
			$this->authenticate();
		}
		if (!$this->authenticated){
			return null;
		}

		$this->exec($command);
		// Enable blocking for both streams
		stream_set_blocking($this->errorStream, true);
		stream_set_blocking($this->stream, true);

		$out[0] = $this->get_output();
		$out[1] = $this->get_stderr();

		fclose($this->errorStream);
		fclose($this->stream);

		$this->disconnect();
		return $out;
	}

	function send_file($local_file, $remote_file){
		if (!$this->connected){
			$this->_connect();
		}
		if (!$this->authenticated){
			$this->authenticate();
		}
		if (!$this->authenticated){
			return null;
		}

		$r = ssh2_scp_send($this->connection, $local_file, $remote_file);

		$this->disconnect();
		return $r;
	}

	function get_file($remote_file, $local_file) {
		if (!$this->connected){
			$this->_connect();
		}
		if (!$this->authenticated){
			$this->authenticate();
		}
		if (!$this->authenticated){
			return null;
		}

		$r = ssh2_scp_recv($this->connection, $remote_file, $local_file);

		$this->disconnect();
		return $r;
	}
}



