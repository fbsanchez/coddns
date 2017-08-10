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


require_once(__DIR__ . "/../../include/config.php");
require_once(__DIR__ . "/../../lib/db.php");
require_once(__DIR__ . "/../../include/functions_util.php");
require_once(__DIR__ . "/../../lib/coduser.php");

if (! defined("_VALID_ACCESS")) { // Avoid direct access
    header ("Location: " . $config["html_root"] . "/");
    exit (1);
}

try {
	$auth_level_required = get_required_auth_level('adm','zones','new');
	$user = new CODUser();
	$user->check_auth_level($auth_level_required);
}
catch (Exception $e) {
	echo $e->getMessage();
	exit (1);
}

?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo $config["html_root"] . "/rs/css/" . $config["html_view"]; ?>/zones.css" />

</head>
<body>
<section>
	<h2>New zone</h2>
	<form id="new_zone" action="<?php echo $config["html_root"]; ?>/?m=adm&z=center#zones" method="POST" onsubmit="fsgo('new_zone','ajax_message','<?php echo $config["html_root"]; ?>/adm/zones/zones_rq_new.php',false,raise_ajax_message);return false;">
		<ul>
			<li><label>Domain</label><input type="text" name="domain" /></li>
			<li><span>Group:</span> <select style="float:right; min-width: 150px;" name="group"> 
				<?php
					// Retrieve all Groups with at least read grant available for current user
					$groups = $user->get_read_groups();

					if (isset($groups["data"])){
						foreach ($groups["data"] as $group) {
				?>
						<option value="<?php echo $group["tag"];?>"><?php echo $group["tag"];?>
						</option>
				<?php
						}
					}
				?>
				</select>

			<li><label>Public zone</label><input type="checkbox" name="public" /></li>
			<li><input type="submit" name="create" value="Create" /></li>
		</ul>
	</form>

	<a class="return" href="<?php echo $config["html_root"] . "/?m=adm&z=center#zones" ?>">Go back</a>	
</section>
</body>
</html>