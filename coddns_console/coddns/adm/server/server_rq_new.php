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

require_once(__DIR__ . "/../../include/config.php");
require_once(__DIR__ . "/../../lib/db.php");
require_once(__DIR__ . "/../../include/functions_util.php");
require_once(__DIR__ . "/../../lib/coduser.php");
require_once(__DIR__ . "/../../lib/codserver.php");


$auth_level_required = get_required_auth_level('adm','server','new');
$user = new CODUser();
$user->check_auth_level($auth_level_required);


$data["tag"]  = secure_get("tag",  "insecure_text");
$data["ip"]   = secure_get("ip",   "url_get");
$data["port"] = secure_get("port", "number");
$data["user"] = secure_get("user", "insecure_text");
$data["pass"] = coddns_encrypt(secure_Get("pass", "base64"));
$data["main_config_file"] = secure_get("conf", "insecure_text");


$server = new CODServer($data);


// validate post data

// escape data received




echo "<pre>";
//var_dump($server);
echo "</pre>";




?>



<p>Servidor no conectado, gestor en construcci&oacute;n</p>
<p>Press "ESC" to stay in this page and add another server</p>
<a class="ajax_button" href="<?php echo $config["html_root"] . "/?m=adm&z=center#servers" ?>">OK</a>