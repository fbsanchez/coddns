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

require_once (dirname(__FILE__) . "/../lib/db.php");
require_once (dirname(__FILE__) . "/../lib/ipv4.php");

// Defines CODUser class

class CODUser {
	var $mail;
	var $time;
	var $pass;
	var $oid;    // user id
	var $groups; // user's groups
	var $rol;
	var $auth_level = null;
	var $logged     = false;
	var $config;

	function CODUser(){
		$this->load_cfg();
		$checks = 0;
		session_start();
		if(isset ($_SESSION["email"])){
			$this->mail = $_SESSION["email"];
			$checks++;
		}
		if(isset ($_SESSION["time"])){
			$this->time = $_SESSION["time"];
			$checks++;
		}
		if(isset ($_SESSION["oid"])){
			$this->oid = $_SESSION["oid"];
			$checks++;
		}
		if(isset ($_SESSION["rol"])){
			$this->rol = $_SESSION["rol"];
			$checks++;
		}
		if(isset ($_SESSION["auth_level"])){
			$this->auth_level = $_SESSION["auth_level"];
			$checks++;
		}
		if ($checks == 5){
			$this->logged = true;
		}

		session_write_close();
		
		// reload auth level
		$this->auth_level = $this->load_auth_level();
	}

	function load_cfg(){
		include (dirname(__FILE__) . "/../include/config.php");
		$this->config = $config;
	}

	function get_is_logged(){
		return $this->logged;
	}

	function get_mail(){
		return $this->mail;
	}
	/**
	 * Process login for a given user/pass
	 *
	 * ARGUMENTS MUST BE CHECKED OUT HERE
	 */
	function login($rq_user, $rq_pass){
		if ($this->logged){
			return null;
		}

		$dbclient = new DBClient($this->config["db_config"]);
		$user = $dbclient->prepare($rq_user, "email");
		$pass = hash ("sha512",$this->config["salt"] . $rq_pass);

		$dbclient->connect() or die ($dbclient->lq_error());

		$q = "Select * from users where lower(mail)=lower('" . $user . "') and pass='" . $pass . "';";
		$r = $dbclient->fetch_object ($dbclient->exeq($q));
		if ($dbclient->lq_nresults() == 0){ // USER NON EXISTENT OR PASSWORD ERROR
		    return null;
		}
		$q = "update users set last_login=now(), ip_last_login='" . _ip() . "' where lower(mail)=lower('" . $user . "');";
		$dbclient->exeq($q) or die($dbclient->lq_error());

		$dbclient->disconnect();

		session_start();
		$_SESSION["email"] = $r->mail;
		$_SESSION["rol"]   = $r->rol;
		$_SESSION["time"]  = time();
		$_SESSION["oid"]   = $r->id;

		$this->user = $r->mail;
		$this->rol  = $r->rol;
		$this->time = $_SESSION["time"];
		$this->oid  = $r->id;

		session_write_close();

		$this->logged = true;
		$this->load_auth_level();

		return $r;

	}
	/**
	 * Process signin for a given user/pass
	 *
	 * ARGUMENTS MUST BE CHECKED OUT HERE
	 */
	function signin($rq_user,$rq_pass){
		if ($this->logged){
			error_log("user already logged");
			return null;
		}

		$dbclient = new DBClient($this->config["db_config"]);
		$user = $dbclient->prepare($_POST["u"], "email");
		$pass = hash ("sha512",$this->config["salt"] . $rq_pass);

		
		$dbclient->connect() or die ($dbclient->lq_error());

		$q = "Select * from users where lower(mail)=lower('" . $user . "');";
		$dbclient->exeq($q) or die ($dbclient->lq_error());
		if ($dbclient->lq_nresults() == 0){ // ADD NEW USER
		    $q = "insert into users (mail,pass, ip_last_login, first_login,rol) "
		    	 . " values (lower('" . $user . "'),'" . $pass . "', '" . _ip() . "', now(),(select id from roles where tag='standar'));";
		    $dbclient->exeq($q) or die ($dbclient->lq_error());
		    $dbclient->disconnect();

		    $q = "Select * from users where lower(mail)=lower('" . $user . "') and pass='" . $pass . "';";
		    $r = $dbclient->get_sql_object($q);

			session_start();
			$_SESSION["email"] = $r->mail;
			$_SESSION["rol"]   = $r->rol;
			$_SESSION["time"]  = time();
			$_SESSION["oid"]   = $r->id;

			$this->user = $r->mail;
			$this->rol  = $r->rol;
			$this->time = $_SESSION["time"];
			$this->oid  = $r->id;

			session_write_close();

			$this->logged = true;
			$this->load_auth_level();

			return $r;
		}
		return null;
	}

	function logout(){
		// TODO
	}

	/**
	 * Load and refresh the access level of the user
	 */
	function load_auth_level(){
		if (!$this->logged)
			return 0;

		// Retrieve rol and auth level from DB
		$dbclient = new DBClient($this->config["db_config"]) or die($dbclient->lq_error());
		$q = "SELECT u.rol,r.auth_level FROM users u, roles r WHERE u.id=" . $this->oid . " and r.id=u.rol;";
		$result = $dbclient->get_sql_object($q);
		if (!isset($result->auth_level)){
			return 0;
		}
		$this->rol        = $result->rol;
		$this->auth_level = $result->auth_level;

		session_start();
		$_SESSION["rol"]        = $this->rol;
		$_SESSION["auth_level"] = $this->auth_level;
		session_write_close();

		return $this->auth_level;
	}

	/**
	 * Alias of load_auth_level
	 */
	function get_auth_level(){
		return $this->load_auth_level();
	}


	function check_auth_level($level=0){
		$auth_level = $this->load_auth_level();

		if (! isset($level)){
			redirect ($this->config["html_root"] . "/err404.html");
			exit(0);
		}

		if ((! isset ($auth_level)) || (! isset($level)) || ( $auth_level < $level) ) {
			die ("Unauthorized to access this content");
			/// redirect ($this->config["html_root"] . "/");
			exit(0);
		}
	}

	function check_auth_level_msg($level=0){
		if ($this->load_auth_level() < $level) {
			die ("Not enough access level to perform that action.");
		}
	}

}



?>