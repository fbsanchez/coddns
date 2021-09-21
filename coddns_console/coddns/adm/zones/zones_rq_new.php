<?php
/**
 * <copyright company="CODDNS">
 * Copyright (c) 2017 All Right Reserved, https://coddns.es
 *
 * THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY
 * KIND, EITHER EXPRESSED OR IMPLIED, NO INCLUDING THE WARRANTIES OF
 * MERCHANTABILITY AND/OR FITNESS FOR A PARTICULAR PURPOSE.
 *
 * </copyright>
 * <author>Fco de Borja Sanchez</author>
 * <email>fborja.sanchezs@gmail.com</email>
 * <date>2017-02-11</date>
 * <update>2017-02-11</udate>
 * <summary> </summary>
 */


require_once __DIR__ . "/../../include/config.php";
require_once __DIR__ . "/../../lib/coduser.php";
require_once __DIR__ . "/../../lib/codzone.php";
require_once __DIR__ . "/../../include/functions_groups.php";


try {
    $auth_level_required = get_required_auth_level('adm', 'zones', 'rq_new');
    $user = new CODUser();
    $user->check_auth_level($auth_level_required);
} catch (Exception $e) {
    echo $e->getMessage();
    exit(1);
}

$dbh = $config["dbh"];

// Get data
$data = array();

$data["id"]        = null; // not created yet
$data["file"]      = null; // not assigned yet
$data["domain"]    = secure_get("domain");
$data["gid"]       = get_group_id(secure_get("group"));
$data["status"]    = time(); // not set yet
$data["is_public"] = (secure_get("public")=="on")?true:false; // null => false, "on" => true


// 1. check spool directory
if (!is_dir($config["spooldir"])) {
    echo "<p>Spool directory <i>" . $config["spooldir"] . "</i> does not exist</p>";
    exit(1);
}

if (strlen($data["domain"]) < MIN_ZONE_STRLEN) {
    echo "<p>Domain name does not accomplish requirements.</p>";
    exit(1);
}

// 2. check zone file does not exist
$zone = new CODZone($data);

try {
    $zone->save();
} catch (Exception $e) {
    echo "<p>" . $e->getMessage() . "</p>";
    ?>
<p>Zone has been sucesfully created!</p>
    <?php
} finally {
    ?>
<p>Press "ESC" to stay in this page and add another zone</p>
<a class="ajax_button" href="<?php echo $config["html_root"] . "/?m=adm&z=center#zones"; ?>">OK</a>
    <?php
}

?>

