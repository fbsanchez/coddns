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

/**
 * Returns an array of zone objects from a given domain
 */
function get_zone_from_domain($domain) {
	global $config;
	$dbh = $config["dbh"];

	$dom = $dbh->prepare($domain,"url_get");

	$q = 'SELECT id from zones where domain="' . $dom . '"';

	$r = $dbh->get_sql_all_objects($q);

	$zones = array();
	if ($r["nitems"] > 0) {
		foreach ($r["data"] as $zone) {
			$z = get_zone_from_id($zone->id);
			if ($z !== false) {
				array_push($zones, $z);
			}
		}
	}

	return $zones;
}

/**
 * Returns a zone object from a zone id
 */
function get_zone_from_id($zone_id) {
	global $config;
	$dbh = $config["dbh"];

	$zid = $dbh->prepare($zone_id, "number");
	$q = "SELECT * from zones where id=$zid";
	$r = $dbh->get_sql_array($q);

	if ($r["nitems"] > 0) {
		return new CODZone($r["data"][0]);
	}

	return false;

}

?>
