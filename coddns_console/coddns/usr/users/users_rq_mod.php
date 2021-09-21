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
require_once __DIR__ . "/../../lib/db.php";
require_once __DIR__ . "/../../include/functions_util.php";
require_once __DIR__ . "/../../lib/coduser.php";

try {
    $auth_level_required = get_required_auth_level('usr', 'users', 'rq_mod');
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

/* CASTELLANO */
/* ENGLISH */
/* DEUTSCH */

if ((! isset($_POST["op"]))
    || (! isset($_POST["np"]))
    || (! isset($_POST["cp"]))
) {
    echo "<div class'err'>Rellene todos los datos</div>";
    exit(1);
}

$rq_opass = (base64_decode($_POST["op"]));
$rq_npass = (base64_decode($_POST["np"]));
$rq_cpass = (base64_decode($_POST["cp"]));

if ($rq_npass != $rq_cpass) {
    echo "<div class='err'>La confirmacion no coincide</div>";
    exit(2);
}

if (( strlen($rq_opass) < MIN_PASS_LENGTH)
    || ( strlen($rq_npass) < MIN_PASS_LENGTH)
    || ( strlen($rq_cpass) < MIN_PASS_LENGTH)
) {
    echo "<div class='err'>No cumple las longitudes m&iacute;nimas</div>";
    exit(2);
}

$dbclient = $config["dbh"];
$salt  = $config["salt"];
$opass = hash("sha512", $salt . $rq_opass);
$npass = hash("sha512", $salt . $rq_npass);
$cpass = hash("sha512", $salt . $rq_cpass);

$q = "Select * from users where lower(mail)=lower('" . $user->get_mail() . "') and pass='" . $opass . "';";
$r = $dbclient->fetch_object($dbclient->exeq($q));
if ($dbclient->lq_nresults() == 0) { // USER NON EXISTENT OR PASSWORD ERROR
    echo "<div class='err'>Los datos introducidos no son correctos</div>";
    exit(3);
}
$q = "Update users set pass='" . $npass . "' where lower(mail)=lower('" . $user->get_mail() . "');";
$dbclient->exeq($q);

echo "<div class='ok'>Contrase&ntilde;a actualizada con &eacute;xito</div>";
