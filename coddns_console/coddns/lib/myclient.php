<?php

/********************************************************************
 * <copyright file="myclient.php" company="ESProject">
 * Copyright (c) 2013 All Right Reserved, http://www.esproject.es/
 *
 * THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY
 * KIND, EITHER EXPRESSED OR IMPLIED, NO INCLUDING THE WARRANTIES OF
 * MERCHANTABILITY AND/OR FITNESS FOR A PARTICULAR PURPOSE.
 *
 * </copyright>
 * <author>Fco de Borja Sanchez</author>
 * <email>dev.esproject@gmail.es</email>
 * <date>2016-01-27</date>
 * <update>2016-01-27</udate>
 * <summary> </summary>
/********************************************************************/
class MyClient{
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

  function MyClient($db_config){

    $this->username = $db_config["username"];
    $this->password = $db_config["password"];
    $this->hostname = $db_config["hostname"];
    $this->port     = $db_config["port"];
    $this->db       = $db_config["name"];
    $this->schema   = $db_config["schema"];
  }

  function connect(){
    $this->link = mysqli_connect("host='"   . $this->hostname .
                             "' port='"     . $this->port .
                             "' dbname='"   . $this->db .
                             "' user='"     . $this->username .
                             "' password='" . $this->password . "'");
    if(!$this->link){
      $this->error = mysqli_error($this->db);
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
    $pgq_ex = mysqli_query($this->link, $query);
    if(!$pgq_ex){
      $this->error = mysqli_error($this->link);
      return null;
    }
    $this->nresults = mysqli_num_rows($pgq_ex);
    return $pgq_ex;
  }

  function lq_error(){
    return $this->error;
  }

  function lq_nresults(){
    return $this->nresults;
  }

  function disconnect(){
    mysqli_close($this->link);
  }
}


?>
