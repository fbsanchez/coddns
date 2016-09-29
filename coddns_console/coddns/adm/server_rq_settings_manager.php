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

require_once(__DIR__ . "/../include/config.php");
require_once(__DIR__ . "/../lib/db.php");
require_once(__DIR__ . "/../lib/util.php");
require_once(__DIR__ . "/../lib/coduser.php");

$auth_level_required = get_required_auth_level('adm','server','rq_settings_manager');
$user = new CODUser();
$user->check_auth_level($auth_level_required);


function check_valid_conf($conf){
	// execute named-checkconf with the received content
	// if pass, backup the old conf file
	// and next update the content.


	// +
	// Save on /var/named/backup the conf files
	// -> allow the user restore a backuped conf file
}


// Update local files 1st
// Check if there's changes on files
// Update server's files

$config = secure_get("gconf_input", "base64");

write_file($config,"/var/named/data/test.txt");

echo $config;


?>

<script>raise_ajax_message();</script>