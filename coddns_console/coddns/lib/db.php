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
require_once (__DIR__ . "/myclient.php");
require_once (__DIR__ . "/pgclient.php");
require_once (__DIR__ . "/../include/functions_util.php");

class DBClient {
  var $username;
  var $password;
  var $hostname;
  var $schema;
  var $port;
  var $db;
  var $last_query = null;
  var $nresutls   = null;
  var $error      = null;
  var $client;
  var $config;

  function DBClient($cfg){
    if ($cfg === null){
      return null;
    }
    $this->engine = $cfg["engine"];

    if ($this->engine == "postgresql") {
      $this->client = new PgClient($cfg);
    }
    elseif ($this->engine == "mysql") {
      $this->client = new MyClient($cfg);
    }
  }

  function is_connected(){
    if (isset ($this->client)) {
      return $this->client->is_connected();
    }
    return false;
  }
  function connect(){
    if($this->is_connected()){
      return true;
    }
    return $this->client->connect();
  }
  function exeq($query) {
    return $this->client->exeq($query);
  }
  function lq_error(){
   return $this->client->lq_error(); 
  }
  function lq_nresults(){
    return $this->client->lq_nresults();
  }
  function disconnect(){
    return $this->client->disconnect();
  }
  function fetch_object($result){
    return $this->client->fetch_object($result);
  }
  function fetch_array($result){
    return $this->client->fetch_array($result);
  }
  function last_id(){
    return $this->client->last_id();
  }

  function do_sql($query){
    if ((isset($this->client)) && ($this->is_connected())) {
      return $this->exeq($query) or die($this->lq_error());
    }
    return false;
  }

  function get_sql_object($query){
    if ((isset($this->client)) && ($this->is_connected())) {
      $r   = $this->exeq($query) or die($this->lq_error());
      $out = $this->fetch_object($r);
      return $out;
    }
    return false;
  }


  function get_sql_all_objects($query) {
    if ((isset($this->client)) && ($this->is_connected())) {
      $r      = $this->exeq($query) or error_log($this->lq_error());
      $nitems = $this->lq_nresults();
      $out    = array();

      // Retrieve all items
      while($tmp = $this->fetch_object($r)) {
        array_push($out, $tmp);
      }
      
      
      return array( "nitems" => $nitems, "data" => $out);
    }
    return false;
  }


  function get_sql_array($query){
    if ((isset($this->client)) && ($this->is_connected())) {
      $r      = $this->exeq($query) or error_log($this->lq_error());
      $nitems = $this->lq_nresults();
      $out    = array();

      // Retrieve all items
      while($tmp = $this->fetch_array($r)) {
        array_push($out, $tmp);
      }
      
      
      return array( "nitems" => $nitems, "data" => $out);
    }
    return false;
  }

  /**
   * DB Utilities
   */

  function date_checker($date){
    list ($y, $m, $d) = explode ("-", $date);
    if(checkdate($m, $d, $y))
      return "$y-$m-$d";
    return null;
  }

  function datetime_checker($date){
    list ($y, $m, $d) = explode ("-",$date);
    if(strstr($d, "T")){
      list ($d, $h) = explode("T",$d);
    }
    else{
      list ($d, $h) = explode(" ",$d);
    }
    list ($h, $mi) = explode(":",$h);

    if ($d>31){
      $t = $y;
      $y = $d;
      $d = $t;
    }

    $h = intval($h);
    $mi = intval($mi);
    if((checkdate($m, $d, $y)) && ($h>=0) && ($h<=23) && ($mi>=0) && ($mi<=59)){
      return "$y-$m-$d $h:$mi";
    } else {
      error_log("Date conversion error: a:$y-m:$m-d:$d h:$h:mi$mi");
      return null;
    }
  }

  /**
   * Clears input for a sql argument
   * XXX: Needs a harder check...
   *
   * Accepts:
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
  function prepare($clsqlarg, $type){
    
    switch($type){
      case "email":     return preg_replace("/[^a-zA-Z0-9.@]/", "", $clsqlarg);
      case "number":    return floatval($clsqlarg);
      case "url_get":   return preg_replace("/[^a-zA-Z0-9_\.-]/", "", $clsqlarg);
      case "letters":   return preg_replace("/[^a-zA-Z0-9]/", "", $clsqlarg);
      case "letters++": return preg_replace("/[^a-zA-Z0-9\.]/", "", $clsqlarg);
      case "ip_addr":   return preg_replace("/[^0-9\.]/", "", $clsqlarg);
      case "insecure_text":{ // there's no need to use this in a permanent connection
        $search  = array("<script", "</script>", "%0A");
        return str_replace("%", "$",(
            urlencode(
              strip_tags (
                str_replace($search,"", $clsqlarg),
                "")
            )));
      }
      case "text":{
        $search  = array("<script", "</script>", "%0A");
        return $this->client->escape_string(
          str_replace("%", "$",(
            urlencode(
              strip_tags (
                str_replace($search,"", $clsqlarg),
                "")
            ))));
      }
      case "rich_text":{
        $search  = array("<script", "</script>", "%0A");
        $replace = array(""       , ""         , "<br>");
        return $this->client->escape_string(
          str_replace("%", "$",(
            urlencode(
              strip_tags (
                str_replace($search,$replace, $clsqlarg),
                "<b><u><p><a>")
            ))));
      }
      case "url": {
        $search  = array("<script", "</script>");
        return $this->client->escape_string(
          str_replace("%", "$",(
            urlencode(
              strip_tags (
                str_replace($search,"", $clsqlarg),
                "")
            ))));
      }
      case "date":{
        if($tmp = $this->date_checker($clsqlarg))
          $date = new DateTime($tmp);
          return $date->format("U"); // return unixtimestamp
        return null;
      }
      case "datetime":{
        if($tmp = $this->datetime_checker($clsqlarg))
          return $tmp;
        return null;
      }
      case "ip":{
        return _ip2long($clsqlarg);
      }
      case "json":{
        return json_decode($clsqlarg);
      }
      case "base64":{
        return base64_decode($clsqlarg);
      }
      default: return null;
    }
  }

  function decode($v,$tflag=null){
    if (isset($tflag)){
      return date("Y-m-d\TH:i:s\Z", $v);
    }
    return urldecode(str_replace("$", "%",($v)));
  }
}

?>
