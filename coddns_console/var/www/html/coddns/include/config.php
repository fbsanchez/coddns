<?php

/*
 * Database configuration
 */
$pg_config = array("username"=>"ddnsp",
                   "password"=>'p4ssw0rd',
                   "hostname"=>"localhost",
                   "port"    =>"5432",
                   "name"    =>"db_ddnsp",
                   "schema"  =>"sch_ddnsp");

$db_type = "pgsql";
$db_config = $pg_config;

$config = array ("domainname" => "example.lan",
		 "html_root"  => "/");

defined ("MIN_USER_LENGTH") or define ("MIN_USER_LENGTH", 4);
defined ("MIN_PASS_LENGTH") or define ("MIN_PASS_LENGTH", 4);
$salt = "Set a custom salt to enforce the passwords";

?>
