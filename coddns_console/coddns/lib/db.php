<?php

require_once (dirname(__FILE__) . "/myclient.php");
require_once (dirname(__FILE__) . "/pgclient.php");

class DBClient {
  var $username;
  var $password;
  var $hostname;
  var $schema;
  var $port;
  var $db;
  var $link = null;
  var $last_query = null;
  var $nresutls = null;
  var $error = null;

  var $client;

  function DBClient($db_config){
    $this->engine = $db_config["engine"];

    if ($this->engine == "postgresql") {
      $this->client = new PgClient($db_config);
    }
    elseif ($this->engine == "mysql") {
      $this->client = new MyClient($db_config);
    }
  }

  function connect(){
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
   */
  function prepare($clsqlarg, $type){
    switch($type){
      case "email":     return strip_tags (preg_replace("/[^a-zA-Z0-9.@]/", "", $clsqlarg), "<b><u><p>");
      case "number":    return floatval($clsqlarg);
      case "letters":   return strip_tags (preg_replace("/[^a-zA-Z0-9]/", "", $clsqlarg), "<b><u><p>");
      case "insecure_text":{
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
        return ip2long($clsqlarg);
      }
      default: return null;
    }
  }

  function decode($v,$tflag){
    if (isset($tflag)){
      return date("Y-m-d\TH:i:s\Z", $v);
    }
    return urldecode(str_replace("$", "%",($v)));
  }
}

?>
