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

require_once(dirname(__FILE__) . "/../include/config.php");

/* USER-AGENTS
================================================== */
function check_user_agent ( $type = NULL ) {
        $user_agent = strtolower ( $_SERVER['HTTP_USER_AGENT'] );
        if ( $type == 'bot' ) {
                // matches popular bots
                if ( preg_match ( "/googlebot|adsbot|yahooseeker|yahoobot|msnbot|watchmouse|pingdom\.com|feedfetcher-google/", $user_agent ) ) {
                        return true;
                        // watchmouse|pingdom\.com are "uptime services"
                }
        } else if ( $type == 'browser' ) {
                // matches core browser types
                if ( preg_match ( "/mozilla\/|opera\//", $user_agent ) ) {
                        return true;
                }
        } else if ( $type == 'mobile' ) {
                // matches popular mobile devices that have small screens and/or touch inputs
                // mobile devices have regional trends; some of these will have varying popularity in Europe, Asia, and America
                // detailed demographics are unknown, and South America, the Pacific Islands, and Africa trends might not be represented, here
                if ( preg_match ( "/phone|iphone|itouch|ipod|symbian|android|htc_|htc-|palmos|blackberry|opera mini|iemobile|windows ce|nokia|fennec|hiptop|kindle|mot |mot-|webos\/|samsung|sonyericsson|^sie-|nintendo/", $user_agent ) ) {
                        // these are the most common
                        return true;
                } else if ( preg_match ( "/mobile|pda;|avantgo|eudoraweb|minimo|netfront|brew|teleca|lg;|lge |wap;| wap /", $user_agent ) ) {
                        // these are less common, and might not be worth checking
                        return true;
                }
        }
        return false;
}
function isOverHTTPS(){
    if (isset($_SERVER["HTTPS"]) && $_SERVER['SERVER_PORT'] == '443')
        return true;
    return false;
}

function redirect($url){
    if (headers_sent()){
      die('<script type="text/javascript">window.location.href="' . $url . '";</script>');
    }else{
      header('Location: ' . $url);
      die();
    }    
}



function secure_get($argument, $mode = "url_get"){
    require_once(dirname(__FILE__) . "/db.php");

    $securizer = new DBClient(null);

    if (isset ($_REQUEST["$argument"])){
        $token = $securizer->prepare($_REQUEST["$argument"], $mode);
        return $token;
    }
    return null;
}

function get_required_auth_level($mode,$zone,$operation){
    require_once(dirname(__FILE__) . "/db.php");
    include(dirname(__FILE__) . "/../include/config.php");

    $dbclient = new DBClient($db_config);
    $sm = $dbclient->prepare($mode, "url_get");
    $sz = $dbclient->prepare($zone, "url_get");
    $so = $dbclient->prepare($operation, "url_get");

    $obj = $dbclient->get_sql_object("SELECT auth_level FROM site_acl WHERE m='$sm' and z='$sz' and op='$so'");

    if (isset($obj->auth_level)){
        return $obj->auth_level;
    }
    return null;

}


?>
