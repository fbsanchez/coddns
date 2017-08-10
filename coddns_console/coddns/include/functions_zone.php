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
 * <date>2017-04-21</date>
 * <update>2017-04-21</udate>
 * <summary> </summary>
 */

require_once (__DIR__ . "/../lib/codzone.php");

function get_zone_from_domain($domain) {
	global $config;
	$dbh = $config["dbh"];

	$dom = $dbh->prepare($domain,"url_get");

	$q = 'SELECT * from zones where domain="' . $dom . '"';

	$r = $dbh->get_sql_array($q);

	if ($r["nitems"] == 1) {
		return new CODZone($r["data"][0]);
	}

	return $r["nitems"];
}



?>
