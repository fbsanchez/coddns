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
if (defined ("__CODCLOUD_PHP__")) {
  return;
}
define ("__CODCLOUD_PHP__", 1);


require_once (__DIR__ . "/../lib/db.php");
require_once (__DIR__ . "/../lib/ipv4.php");

// Defines CODUser class

class CODCloud {
	var $ip;
	var $port;
	var $pass;
	var $user;
	var $status;
	
}



?>