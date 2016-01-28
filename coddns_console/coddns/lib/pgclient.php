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

  function fetch_object($result){
    return pg_fetch_object($result);
  }
  function fetch_array($result){
    return pg_fetch_array($result);
  }
}


?>
