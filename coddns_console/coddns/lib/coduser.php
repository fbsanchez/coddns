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
require_once (__DIR__ . "/../include/functions_ip.php");

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
	var $auth_token;

	function CODUser($auth_token = null){
		$this->load_cfg();
		$checks = 0;
		if (session_status() == PHP_SESSION_NONE){
			session_start();
		}
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
		if (session_status() != PHP_SESSION_NONE){
			session_write_close();
		}
		
		if(isset($auth_token)){
			$this->auth_token = $auth_token;
		}
		else {
			$this->auth_token = false;
		}
		
		// reload auth level
		$this->auth_level = $this->load_auth_level();
	}

	function load_cfg(){
		if (empty ($this->config)) {
			include (__DIR__ . "/../include/config.php");
			$this->config = $config;
		}
	}

	function get_is_logged(){
		return $this->logged;
	}

	/**
	 * Return all available groups for current user with RR grant or higher
	 */
	function get_read_groups(){
		global $config;
		$dbclient = $config["dbh"];
		$q = "select g.id, g.tag from groups g, tusers_groups ug, users u where ug.oid=u.id and ug.gid=g.id and u.mail='" . $this->mail . "' and (ug.view=1 or ug.edit=1 or ug.admin=1);";
		return $dbclient->get_sql_array($q);
	}

	function get_mail(){
		return $this->mail;
	}

	/**
	 * Process login for a given user/pass
	 *
	 * ARGUMENTS MUST BE CHECKED OUT HERE
	 */
	function login($rq_user, $rq_pass) {
		global $config;
		$this->load_cfg();
		if ($this->logged){
			return false;
		}

		$dbclient = $config["dbh"];
		$user = $dbclient->prepare($rq_user, "email");
		$pass = hash ("sha512",$this->config["salt"] . $rq_pass);


		$q = "Select * from users where lower(mail)=lower('" . $user . "') and pass='" . $pass . "';";
		$r = $dbclient->fetch_object ($dbclient->exeq($q));
		if ($dbclient->lq_nresults() == 0){ // USER NON EXISTENT OR PASSWORD ERROR
		    return null;
		}
		$q = "update users set last_login=now(), ip_last_login='" . $dbclient->prepare(_ip(), "ip") . "' where lower(mail)=lower('" . $user . "');";
		$dbclient->exeq($q) or die($dbclient->lq_error());

		if (session_status() == PHP_SESSION_NONE){
			session_start();
		}
		$_SESSION["email"] = $r->mail;
		$_SESSION["rol"]   = $r->rol;
		$_SESSION["time"]  = time();
		$_SESSION["oid"]   = $r->id;

		$this->mail = $r->mail;
		$this->rol  = $r->rol;
		$this->time = $_SESSION["time"];
		$this->oid  = $r->id;


		$this->logged = true;
		$this->load_auth_level();

		session_write_close();
		return $r;

	}
	/**
	 * Process signin for a given user/pass
	 *
	 * ARGUMENTS MUST BE CHECKED OUT HERE
	 */
	function signin($rq_user,$rq_pass){
		global $config;
		if ($this->logged){
			error_log("user already logged");
			return null;
		}

		$dbclient = $config["dbh"];
		$user = $dbclient->prepare($_POST["u"], "email");
		$pass = hash ("sha512",$this->config["salt"] . $rq_pass);

		$q = "Select * from users where lower(mail)=lower('" . $user . "');";
		$dbclient->exeq($q) or die ($dbclient->lq_error());
		if ($dbclient->lq_nresults() == 0){ // ADD NEW USER
		    $q = "insert into users (mail,pass, ip_last_login, first_login,rol) "
		    	 . " values (lower('" . $user . "'),'" . $pass . "', '" . $dbclient->prepare(_ip(),"ip") . "', now(),(select id from roles where tag='standar'));";
		    $dbclient->exeq($q) or die ($dbclient->lq_error());
		    // Add user to "private" group
		    $q = "insert into tusers_groups (gid,oid,edit)  values ((select id from groups where tag='private'), (select id from users where lower(mail)=lower('" . $user . "')),1 );";
		    $dbclient->exeq($q) or die ($dbclient->lq_error());


		    $q = "Select * from users where lower(mail)=lower('" . $user . "') and pass='" . $pass . "';";
		    $r = $dbclient->get_sql_object($q);

			if (session_status() == PHP_SESSION_NONE){
				session_start();
			}
			$_SESSION["email"] = $r->mail;
			$_SESSION["rol"]   = $r->rol;
			$_SESSION["time"]  = time();
			$_SESSION["oid"]   = $r->id;

			$this->mail = $r->mail;
			$this->rol  = $r->rol;
			$this->time = $_SESSION["time"];
			$this->oid  = $r->id;

			$this->logged = true;
			$this->load_auth_level();

			session_write_close();

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
	function load_auth_level($auth_token = null){
		global $config;
		if ((!$this->logged) && (!$this->auth_token)) {
			return 0;
		}

		// Retrieve rol and auth level from DB
		$dbclient = $config["dbh"];


		if((isset($this->auth_token)) && ($this->auth_token != "")) {
			$q = "SELECT u.rol,r.auth_level FROM users u, roles r WHERE u.auth_token=" . $this->auth_token. " and r.id=u.rol;";
		}
		else {
			$q = "SELECT u.rol,r.auth_level FROM users u, roles r WHERE u.id=" . $this->oid . " and r.id=u.rol;";	
		}
		
		$result = $dbclient->get_sql_object($q) or die("Cannot connect");
		if (!isset($result->auth_level)){
			return 0;
		}

		$this->rol        = $result->rol;
		$this->auth_level = $result->auth_level;

		$_SESSION["rol"]        = $this->rol;
		$_SESSION["auth_level"] = $this->auth_level;

		return $this->auth_level;
	}

	/**
	 * Alias of load_auth_level
	 */
	function get_auth_level(){
		return $this->load_auth_level();
	}


	function check_auth_level($level=0){
		global $config;
		$auth_level = $this->load_auth_level();

		$error = "Unauthorized to access this content";

		if (! isset($level)){
			throw new Exception ($error);
			//redirect ($this->config["html_root"] . "/?z=err404");
		}

		if ((! isset ($auth_level)) || (! isset($level)) || ( $auth_level < $level) ) {
			throw new Exception ($error);
			/// redirect ($this->config["html_root"] . "/");
		}
	}

	function is_global_admin() {
		global $config;
		if (!$this->logged)
			return false;
		$dbclient = $config["dbh"];
		$q = "select auth_level from roles where tag='admin'";
		$r = $dbclient->get_sql_object($q);
		if(isset($r->auth_level)) {
			if($r->auth_level <= $this->load_auth_level()){
				return true;
			}
		}
		return false;
	}

	function check_auth_level_msg($level=0){
		if ($this->load_auth_level() < $level) {
			die ("Not enough access level to perform that action.");
		}
	}

	function int_check_grants_over_item($grants="(ug.view=1 or ug.admin=1)", $item, $item_type="host"){
		global $config;
		if (!$this->logged)
			return false;
		$dbclient = $config["dbh"];
		switch ($item_type){
			case "host":
				$q = "select count(tag) as n from hosts where ((oid=(select id from users where lower(mail)=lower('" . $this->mail . "')) and gid in (select id from groups where tag='private')) or (gid in (select g.id from groups g, tusers_groups ug, users u where u.id=ug.oid and g.id=ug.gid and " . $grants ." and lower(u.mail)=lower('" . $this->mail . "')))) and lower(tag)=lower('" . $item . "');";
				$result = $dbclient->get_sql_object($q);
				if ($result->n >= 1){
					return true;
				}
			break;
			case "zone":
			$q = "select count(*) as n from (select z.domain from zones z, tusers_groups ug, users u where z.gid=ug.gid and ug.oid=u.id and mail='" . $this->mail . "' and " . $grants . " and lower(z.domain)=lower('" . $item . "')) UNION (select z.domain from zones z, tusers_groups ug, users u where z.is_public=1) t";
				$result = $dbclient->get_sql_object($q);
				if ($result->n >= 1){
					return true;
				}
			break;
			default:
			break;
		}

		return false;
	}

	function check_grant_over_item($grants, $item,$item_type="host"){
		if (!$this->logged)
			return false;

		if ($this->is_global_admin()) {
			return true;
		}

		switch ($grants){
			case "read":
				return $this->int_check_grants_over_item("(ug.view=1 or ug.admin=1)", $item, $item_type="host");
			break;
			case "write":
				return $this->int_check_grants_over_item("(ug.edit=1 or ug.admin=1)", $item, $item_type="host");
			break;
			case "admin":
				return $this->int_check_grants_over_item("(ug.admin=1)", $item, $item_type="host");
			break;
			default:
			break;
		}
		return false;
	}


}



?>
