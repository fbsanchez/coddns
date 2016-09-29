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

require_once (__DIR__ . "/db.php");
require_once (__DIR__ . "/ipv4.php");

// Defines CODUser class

class CODServer {
	var $ip;
	var $port;
	var $pass;
	var $user;
	var $status;
	var $dbclient;
	var $config;

	public function __construct(){
		$this->load_cfg();
		// No actions.
	}
	
	/**
	 * Initializes a CODServer object based on
	 * 	the name of the server
	 * @param [type] $server_name [description]
	 * @return Int returns the ID of the server (if any)
	 */
	protected function CODServer($server_name) {
		$this->load_cfg();
		$this->dbclient = new DBClient($this->config["dbconfig"]);

	}

	private function load_cfg(){
		if (empty($this->config)){
			include (__DIR__ . "/../include/config.php");
			$this->config = $config;
		}
	}
}



?>