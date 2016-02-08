<?php

/*
 * Database configuration
 */
$db_config = array("engine"  =>"mysql", // Could be mysql or postgresql
                   "username"=>"coddns",
                   "password"=>'c0DdnS',
                   "hostname"=>"localhost",
                   "port"    =>"3306",
                   "name"    =>"coddns",
                   "schema"  =>"");

// domain name: FQDN base for the system
// html_root: if you want to access http://yousite.yourdomain/coddns
//            set it to /coddns, is the nav location
$config = array ("domainname" => "coddns.test",
		 "html_root"  => "");

$salt = "MTQ1NDk1NzYyMA==";

defined ("MIN_USER_LENGTH") or define ("MIN_USER_LENGTH", 4);
defined ("MIN_PASS_LENGTH") or define ("MIN_PASS_LENGTH", 4);
defined ("MIN_HOST_LENGTH") or define ("MIN_HOST_LENGTH", 1);
defined ("MAX_HOST_LENGTH") or define ("MAX_HOST_LENGTH", 200);

?>
