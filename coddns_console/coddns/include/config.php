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
$config = array ("domainname" => "coddns.test",
		 "html_root"  => "");

defined ("MIN_USER_LENGTH") or define ("MIN_USER_LENGTH", 4);
defined ("MIN_PASS_LENGTH") or define ("MIN_PASS_LENGTH", 4);
$salt = "Set a custom salt to enforce the passwords";

defined ("LENGTH_USER_MIN") or define ("LENGTH_USER_MIN", 2);
defined ("LENGTH_PASS_MIN") or define ("LENGTH_PASS_MIN", 2);
defined ("LENGTH_HOST_MIN") or define ("LENGTH_HOST_MIN", 1);
defined ("LENGTH_HOST_MAX") or define ("LENGTH_HOST_MAX", 200);

?>
