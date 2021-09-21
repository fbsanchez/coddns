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


function get_group_id($group_tag)
{
    global $config;
    $dbh = $config["dbh"];

    $gt = $dbh->prepare($group_tag, "url_get");

    if (!isset($gt)) {
        return null;
    }

    $r = $dbh->get_sql_object('SELECT id from `groups` where tag="' . $gt . '"');
    
    if (isset($r)) {
        return $r->id;
    }
    return null;
}

function get_group_name($group_id)
{
    global $config;
    $dbh = $config["dbh"];

    $gid = $dbh->prepare($group_id, "number");

    if (!isset($gid)) {
        return null;
    }

    $r = $dbh->get_sql_object('SELECT tag from `groups` where id=' . $gid);
    
    if (isset($r)) {
        return $r->tag;
    }
    return null;
}
