<?php

/*
 * Database configuration
 */
$pg_config = array("engine"  =>"mysql", // Could be mysql or postgresql
                   "username"=>"coddns",
                   "password"=>'coddns',
                   "hostname"=>"localhost",
                   "port"    =>"3306",
                   "name"    =>"coddns",
                   "schema"  =>"");

$db_config = $pg_config;

// domain name: fqdn base for the system
// html_root: if you want to access http://yousite.yourdomain/coddns
//            set it to /coddns, is the nav location
$config = array ("domainname" => "example.lan",
		 "html_root"  => "/coddns");

defined ("MIN_USER_LENGTH") or define ("MIN_USER_LENGTH", 4);
defined ("MIN_PASS_LENGTH") or define ("MIN_PASS_LENGTH", 4);
$salt = "Set a custom salt to enforce the passwords";

?>
