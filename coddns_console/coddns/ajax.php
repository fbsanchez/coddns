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
 * <date>2016-03-01</date>
 * <update>2016-03-01</udate>
 * <summary> </summary>
 */

require_once (dirname(__FILE__) . "/include/config.php");
require_once (dirname(__FILE__) . "/lib/db.php");
require_once (dirname(__FILE__) . "/lib/util.php");
require_once (dirname(__FILE__) . "/lib/coduser.php");

if (! defined("_VALID_ACCESS")) { // Avoid direct access
    header ("Location: " . $config["html_root"] . "/");
    exit (1);
}

$auth_level_required = get_required_auth_level('ajax','','');
$user = new CODUser();
$user->check_auth_level($auth_level_required);

?>