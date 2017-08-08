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
require_once (__DIR__ . "/db.php");
require_once (__DIR__ . "/../include/functions_ip.php");
require_once (__DIR__ . "/../include/functions_util.php");

// Defines CODZone class

class CODZone {
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

	function CODZone($data) {
		$this->id        = $data["id"];
		$this->file      = $data["file"];
		$this->domain    = $data["domain"];
		$this->gid       = $data["gid"];
		$this->status    = $data["status"];
		$this->is_public = $data["is_public"];
	}

	/**
	 * Has file
	 */
	function has_file() {
		global $config;

		if (isset ($this->file)) {
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
	function save(){
		global $config;
		$dbh = $config["dbh"];

		if (!isset($this->id)) {
			// No ID set, create a new entry in DB
			// 1. search for duplicates
			$r = $dbh->do_sql('SELECT count(*) from zones where domain="' . $this->domain . '"');
			if ($r->nresults > 0) {
				throw new Exception ("Zone already defined in DB.");
			}
			// 2. add to DB
			$r = $dbh->do_sql('INSERT INTO zones (domain,gid,config,status,is_public) VALUES ('
				. '"' . $this->domain . '",'
				. $this->gid . ","
				. '"' . $this->config . '",'
				. $this->status . ','
				. ($this->is_public?"1":"0")
				. ')');
		}
	}

}
