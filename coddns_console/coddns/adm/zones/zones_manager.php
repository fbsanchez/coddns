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

require_once(__DIR__ . "/../../include/config.php");
require_once(__DIR__ . "/../../lib/db.php");
require_once(__DIR__ . "/../../include/functions_util.php");
require_once(__DIR__ . "/../../include/functions_zone.php");
require_once(__DIR__ . "/../../include/functions_groups.php");
require_once(__DIR__ . "/../../lib/coduser.php");

if (! defined("_VALID_ACCESS")) { // Avoid direct access
    header ("Location: " . $config["html_root"] . "/");
    exit (1);
}

try {
	$auth_level_required = get_required_auth_level('adm','zones','manager');
	$user = new CODUser();
	$user->check_auth_level($auth_level_required);
}
catch (Exception $e) {
	echo $e->getMessage();
	exit (1);
}


if(!isset($servername)){
	$domain = secure_get("id");
}

$zone = get_zone_from_domain($domain);
?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo $config["html_root"] . "/rs/css/" . $config["html_view"]; ?>/zones.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $config["html_root"] . "/rs/css/" . $config["html_view"]; ?>/zone_manager.css" />

<script type="text/javascript">
	function show_editor() {
		copyContent('raw','file');
		document.getElementById('zone_file').style["max-height"] = "500px";
	}
</script>
</head>
<body>
<section>
	<h2>Zone manager</h2>
	<form action="#" method="POST" onsubmit="return false;">
		<ul>
			<li><label>Domain</label><input type="text" name="domain" value="<?php echo $zone->domain;?>"/></li>
			<li><label>Group</label><select class="input_select" name="group">
				<?php
				$groups = $user->get_read_groups();
				$group_name = get_group_name($zone->gid);

					if (isset($groups["data"])){
						foreach ($groups["data"] as $group) {
				?>
						<option value="<?php echo $group["tag"];?>" <?php

						if ($group_name == $group["tag"]) {
							echo "selected";
						}

						?>><?php echo $group["tag"];?>
						</option>
				<?php
						}
					}
				?>
			</select></li>
			<li><label>Public</label><input type="checkbox" name="pub" checked="<?php echo ($zone->is_public>0?"yes":"no");?>" /></li>
			<li><label>Assigned servers</label>
				<ul class="lvl2">
					<?php
					if (count($zone->servers) == 0) {
						echo "<li>No server assigned</li>";
					}
					foreach ($zone->servers as $server) {
						echo "<li>" . get_server_name($server) . "</li>";
					}
					?>
				</ul>

			</li>
			<li><label>Zone file</label><?php
			if ($zone->file !== null) {
				?>
				<div style="float: right;"><a href="#" onclick="show_editor();">Edit zone file</a></div>
				<?php
			}
			else {
				?>
				<div style="float: right;">Not found. <a href="#" onclick="show_editor();">Create zone file</a></div>
				<?php
			}
			?></li>
		</ul>
	</form>
	<!-- form: zone file editor -->
	<form id="zone_file" method="POST" action="#" onsubmit="fsgo('zone_file','ajax_message','<?php echo $config["html_root"]; ?>/adm/zones/zones_rq_manager.php',false,raise_ajax_message);return false;">
		<h4>Zone editor</h4>
		<input type="hidden" id="file" name="f" />
		<center>
			<label>Zone file:</label><input type="text" name="file" required="yes" />
		</center>
		<textarea id="raw" onkeydown="grow(this);" onchange="copyContent('raw','file');" id="zone_editor"><?php
		echo '$ORIGIN ' . $zone->domain . ".\n";
		echo '$TTL 604800     ; 1 week' . "\n";
		echo '@              IN SOA  ns1 admin (' . "\n";
        echo '                        ' . date("Ymd") . ($zone->id%10) . ' ; serial' . "\n";
        echo '                        604800    ; refresh (1 week)' . "\n";
        echo '                        86400     ; retry (1 day)' . "\n";
        echo '                        2419200   ; expire (4 weeks)' . "\n";
        echo '                        604800    ; minimum (1 week)' . "\n";
        echo '                        )' . "\n";
        echo '                NS      ns1' . "\n";
        echo '                A       127.0.0.1' . "\n";
        echo "ns1	IN	A	127.0.0.1\n";

		?>
		</textarea>
		<input style="float:right;" type="submit" <?php 
			if ($zone->file !== null) {
				echo 'name="update" value="Update"';
			}
			else {
				echo 'name="create" value="Create"';
			}
		?> />
	</form>
	<a class="return" href="<?php echo $config["html_root"] . "/?m=adm&z=center#zones" ?>">Go back</a>
</section>
</body>
</html>

