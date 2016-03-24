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
 */

require_once (dirname(__FILE__) . "/../lib/db.php");
require_once (dirname(__FILE__) . "/../lib/ipv4.php");

// Defines CODUser class

class SSHClient {
	var $ip   = null;
	var $user = null;
	var $pass = null;

	var $connection    = false;
	var $authenticated = false;
	var $connected     = false;

	var $stream = null;

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

		return $this;

	}

	function load_cfg(){
		include (dirname(__FILE__) . "/../include/config.php");
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
	 */
	function connect(){
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

		$this->stream = ssh2_exec($this->connection, $command);

		return $this->stream;
	}

	function get_last_execution_output(){
		return $this->stream;
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
}



