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
if (defined ("__CODSERVER_PHP__")) {
  return;
}
define ("__CODSERVER_PHP__", 1);

require_once (__DIR__ . "/db.php");
require_once (__DIR__ . "/ipv4.php");
require_once (__DIR__ . "/util.php");

// Defines CODUser class

class CODServer {
	var $ip;
	var $port;
	var $pass;
	var $user;
	var $status;
	var $config;

	/**
	 * Initializes a CODServer object based on
	 * 	the name of the server
	 * @param [type] $server_name [description]
	 * @return Int returns the ID of the server (if any)
	 */
	public function CODServer($server_name) {
		$this->load_cfg();
		$dbclient = new DBClient($this->config["db_config"]);
		$secure_name = $dbclient->prepare($server_name, "text");
		$query = 'select * from servers where tag="' . $server_name . '"';
		$r = $dbclient->get_sql_object($query);
		if (isset($r)){
			$this->ip     = long2ip($r->ip);
			$this->id     = $r->id;
			$this->gid    = $r->gid;
			$this->name   = $r->tag;
			$this->port   = $r->port;
			$this->pass   = $r->pass;
			$this->user   = $r->srv_user;
			$this->pass   = $r->srv_password;
			$this->status = $r->status;
			$this->main_config_file = $r->main_config_file;
			$this->fingerprint      = $r->fingerprint;
		}
		return $r;
	}

	private function load_cfg(){
		if (empty($this->config)){
			include (__DIR__ . "/../include/config.php");
			$this->config = $config;
		}
	}

	// Getters
	
	function get_id(){
		return $this->id;
	}

	function has_credentials(){
		if (isset($this->user) && (isset($this->pass))) {
			return true;
		}
		return false;
	}

	function set_credentials($user,$pass){
		$this->load_cfg;
		$dbclient = new DBClient($this->config["db_config"]);
		$dbclient->connect();

		$secured_user = $dbclient->prepare($user, "text");
		$secured_pass = coddns_encrypt($pass);

	}

}



?>
