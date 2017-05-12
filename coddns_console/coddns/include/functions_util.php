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


/**
 * Read extra configuration from DB
 */
function load_extra_config($cnf){
    global $config;
    
    include_once (__DIR__ . "/../lib/db.php");
    $cnf["dbh"] = new DBClient($cnf["db_config"]);

    $cnf["dbh"]->connect() or die ($cnf["dbh"]->lq_error());
    $q = "select * from settings;";
    $r = $cnf["dbh"]->exeq($q) or die ($cnf["dbh"]->lq_error());
    while ($row = $cnf["dbh"]->fetch_array ($r) ) {
        $cnf[$row["field"]] = $row["value"];
    }

    return $cnf;
}


/**
 * Encrypt (basic)
 */
function coddns_encrypt($str, $password = null, $iv = null){
    global $config;

    $out = "";

    if (!isset($password)){
        $password = $config["salt"];
    }

    $hash = substr(base64_encode(hash_hmac("sha256",$password,'',true)), 0, 16);

    if (!isset($iv)) {
        $iv = "1234123443214321";
    }

    $str = openssl_encrypt($str, "AES-128-CBC", $hash, true, $iv);

    $b64str = base64_encode($str);

    return $b64str;
}

/**
 * Decrypt (basic)
 */
function coddns_decrypt($str){
    global $config;

    $out = "";

    if (!isset($password)){
        $password = $config["salt"];
    }

    $hash = substr(base64_encode(hash_hmac("sha256",$password,'',true)), 0, 16);

    if (!isset($iv)) {
        $iv = "1234123443214321";
    }

    $str = openssl_decrypt(base64_decode($str), "AES-128-CBC", $hash,true,$iv) . "\n";

    return $str;
}


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

/**
 * Returns if the page is being viewed through HTTPS protocol
 */
function isOverHTTPS(){
    if (isset($_SERVER["HTTPS"]) && $_SERVER['SERVER_PORT'] == '443')
        return true;
    return false;
}

/**
 * Redirect the client to the given url
 */
function redirect($url){
    if (headers_sent()){
      die('<script type="text/javascript">window.location.href="' . $url . '";</script>');
    }else{
      header('Location: ' . $url);
      die();
    }    
}

/**
 * Reads a file and stores it in a string, or prints it to output
 */
function read_file($filepath,$mode = "r", $tostring = false){
    $file = @fopen($filepath, $mode);
    $includes = array();
    $str = "";
    if ($file) {
        while (($buffer = fgets($file)) !== false) {
            if (preg_match("/include +\"(.*?)\"/", $buffer, $tmp)) {
                array_push($includes, $tmp[1]);
            }

            if($tostring === false){
                echo $buffer;
            }
            else {
                $str .= $buffer;
            }
        }
        if (!feof($file)) {
            echo "Failed to open $filepath in $mode mode<br>";
        }
        fclose($file);
    }

    if ($tostring != false) {
        $tostring = $str;
    }
    else {
        return $includes;
    }
}


/**
 * Reads a file and stores it in a string, or prints it to output
 */
function write_file($content, $filepath,$mode = "w"){
    if (!isset($content)){
        return null;
    }
    $file = @fopen($filepath, $mode);
    if ($file) {
        fwrite($file, $content);
        fclose($file);
        return 1;
    }
    return null;
}


/**
 * Securize an argument passed to the script
 * Modes accepted:
 * email
 * number
 * url_get
 * letters
 * letters++
 * ip_addr
 * insecure_text
 * text
 * rich_text
 * url
 * date
 * datetime
 * ip
 * json
 * base64
 */
function secure_get($argument, $mode = "url_get"){
    require_once(__DIR__ . "/../lib/db.php");
    global $config;

    $securizer = $config["dbh"];

    if (isset ($_REQUEST["$argument"])){
        $token = $securizer->prepare($_REQUEST["$argument"], $mode);
        return $token;
    }
    return null;
}

/**
 * Returns the required auth_level for a page
 */
function get_required_auth_level($mode,$zone,$operation){
    require_once(__DIR__ . "/../lib/db.php");
    global $config;

    $dbclient = $config["dbh"];
    $sm = $dbclient->prepare($mode, "url_get");
    $sz = $dbclient->prepare($zone, "url_get");
    $so = $dbclient->prepare($operation, "url_get");

    $obj = $dbclient->get_sql_object("SELECT auth_level FROM site_acl WHERE m='$sm' and z='$sz' and op='$so'");

    if (isset($obj->auth_level)){
        return $obj->auth_level;
    }
    return null;

}


/**
 * _ip2long returns ip2long if target ip is a valid IPv4 
 */
function _ip2long($target) {
    global $config;
    if (preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $target)) {
        return ip2long($target);
    }
    return $config["dbh"]->prepare($target, "url_get");
}


/**
 * _long2ip returns a valid IPv4 or FQDN of target
 */
function _long2ip($target) {
    global $config;
    if (preg_match('/\d+/', $target)) {
        return long2ip($target);
    }
    return $config["dbh"]->prepare($target, "url_get");
}


/**
 * returns the tag associated to a group
 */
function get_group_name($gid) {
    global $config;
    if (!isset($gid)) {
        return false;
    }

    $r = $config["dbh"]->get_sql_object("select tag from groups where id=" . $config["dbh"]->prepare($gid,"number"));

    return $r->tag;
}

?>
