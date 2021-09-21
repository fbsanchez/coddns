<?php
/**
 * <copyright company="CODDNS">
 * Copyright (c) 2017 All Right Reserved, http://coddns.es
 *
 * THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY
 * KIND, EITHER EXPRESSED OR IMPLIED, NO INCLUDING THE WARRANTIES OF
 * MERCHANTABILITY AND/OR FITNESS FOR A PARTICULAR PURPOSE.
 *
 * </copyright>
 * <author>Fco de Borja Sanchez</author>
 * <email>fborja.sanchezs@gmail.com</email>
 * <date>2017-08-10</date>
 * <update>2017-08-10</udate>
 * <summary> </summary>
 */


// Min/max lenghts
defined("MIN_USER_LENGTH") or define("MIN_USER_LENGTH", 4);
defined("MIN_PASS_LENGTH") or define("MIN_PASS_LENGTH", 4);
defined("MIN_HOST_LENGTH") or define("MIN_HOST_LENGTH", 1);
defined("MAX_HOST_LENGTH") or define("MAX_HOST_LENGTH", 200);
defined("MIN_DB_LENGTH")   or define("MIN_DB_LENGTH", 2);
defined("MIN_ZONE_STRLEN") or define("MIN_ZONE_STRLEN", 2);

// Default SSH options
defined("DEFAULT_SSH_PORT") or define("DEFAULT_SSH_PORT", 22);

// Default configuration settins
defined("ITEMS_PER_PAGE") or define("ITEMS_PER_PAGE", 15);


if (! defined("_VALID_INCLUDE")) { // Avoid direct access
    if (!is_file(__DIR__ . "/config.php")) {
        redirect(dirname($_SERVER["REQUEST_URI"]) . "/../");
        exit(1);
    }
    include __DIR__ . "/config.php";
    redirect($config["html_root"] . "/");
    exit(1);
}
