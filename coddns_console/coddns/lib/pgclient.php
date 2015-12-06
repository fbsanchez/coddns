<?php

/********************************************************************
 * <copyright file="pgclient.php" company="ESProject">
 * Copyright (c) 2013 All Right Reserved, http://www.esproject.es/
 *
 * THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY
 * KIND, EITHER EXPRESSED OR IMPLIED, NO INCLUDING THE WARRANTIES OF
 * MERCHANTABILITY AND/OR FITNESS FOR A PARTICULAR PURPOSE.
 *
 * </copyright>
 * <author>Fco de Borja Sanchez</author>
 * <email>dev.esproject@gmail.es</email>
 * <date>2013-11-04</date>
 * <update>2013-11-05</udate>
 * <summary> </summary>
/********************************************************************/
class PgClient{
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

  function PgClient($db_config){

    $this->username = $db_config["username"];
    $this->password = $db_config["password"];
    $this->hostname = $db_config["hostname"];
    $this->port     = $db_config["port"];
    $this->db       = $db_config["name"];
    $this->schema   = $db_config["schema"];
  }

  function connect(){
    $this->link = pg_connect("host='"     . $this->hostname .
                             "' port='"     . $this->port .
                             "' dbname='"   . $this->db .
                             "' user='"     . $this->username .
                             "' password='" . $this->password . "'");
    if(!$this->link){
      $this->error = pg_errormessage($this->db);
      return false;
    }
    $this->exeq("set search_path to '" . $this->schema . "'");
    return true;
  }

  /**
   * Beware of use without prepare the query first
   */
  function exeq($query){
    $this->last_query = $query;
    $pgq_ex = pg_query($this->link, $query);
    if(!$pgq_ex){
      $this->error = pg_last_error($this->link);
      return null;
    }
    $this->nresults = pg_num_rows($pgq_ex);
    return $pgq_ex;
  }

  function lq_error(){
    return $this->error;
  }

  function lq_nresults(){
    return $this->nresults;
  }

  function disconnect(){
    pg_close($this->link);
  }

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
    echo "a:$y-m:$m-d:$d h:$h:mi$mi";
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
    case "text":{
      $search  = array("<script", "</script>", "%0A");
      return str_replace("%", "$",(
              urlencode(
                strip_tags (
                  str_replace($search,"", $clsqlarg),
                  "")
              )));
    }
    case "rich_text":{
      $search  = array("<script", "</script>", "%0A");
      $replace = array(""       , ""         , "<br>");
      return str_replace("%", "$",(
              urlencode(
                strip_tags (
                  str_replace($search,$replace, $clsqlarg),
                  "<b><u><p><a>")
              )));
    }
    case "url": {
      $search  = array("<script", "</script>");
      return str_replace("%", "$",(
              urlencode(
                strip_tags (
                  str_replace($search,"", $clsqlarg),
                  "")
              )));
    }
    case "date":{
        if($tmp = $this->date_checker($clsqlarg))
          return $tmp;
        return null;
      }
    case "datetime":{
        if($tmp = $this->datetime_checker($clsqlarg))
          return $tmp;
        return null;
      }
    default: return null;
    }
  }

  function decode($v){
    return urldecode(str_replace("$", "%",($v)));
  }


}


?>
