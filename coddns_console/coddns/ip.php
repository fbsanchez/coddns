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

require_once __DIR__ . "/include/config.php";
require_once __DIR__ . "/include/functions_ip.php";
require_once __DIR__ . "/include/functions_util.php";
require_once __DIR__ . "/lib/coduser.php";

try {
    $auth_level_required = get_required_auth_level('', 'ip', '');
    $user = new CODUser();
    $user->check_auth_level($auth_level_required);
} catch (Exception $e) {
    echo $e->getMessage();
    exit(1);
}

session_start();
if (!isset($_SESSION["lan"])) {
    $_SESSION["lan"] = "es";
}
$lan = $_SESSION["lan"];
session_write_close();

echo json_encode(_ip());
