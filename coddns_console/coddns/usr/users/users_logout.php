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

require_once __DIR__ . "/../../include/config.php";
require_once __DIR__ . "/../../include/functions_util.php";
require_once __DIR__ . "/../../lib/coduser.php";

if (! defined("_VALID_ACCESS")) { // Avoid direct access
    header("Location: " . $config["html_root"] . "/");
    exit(1);
}

try {
    $auth_level_required = get_required_auth_level('usr', 'users', 'logout');
    $user = new CODUser();
    $user->check_auth_level($auth_level_required);
} catch (Exception $e) {
    echo $e->getMessage();
    exit(1);
}

session_start();
session_destroy();
session_write_close();

redirect($config["html_root"] . '/?lang=' . $lan);
