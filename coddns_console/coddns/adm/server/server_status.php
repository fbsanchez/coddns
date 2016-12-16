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
require_once(__DIR__ . "/../../lib/util.php");
require_once(__DIR__ . "/../../lib/coduser.php");

$auth_level_required = get_required_auth_level('adm','server','status');
$user = new CODUser();
$user->check_auth_level($auth_level_required);


$servername = secure_get("id");

if(!isset($servername)){
	echo "<pre>";
	var_dump($_REQUEST);
	echo "</pre>";
	die ("Please specify a server");
}



$dbclient = new DBClient($db_config);
$r = $dbclient->get_sql_object("Select * from servers where tag='$servername'");

if (empty($r)){
	echo "No hay servidores registrados con ese nombre.";
	return 0;
}
?>


<!DOCTYPE HTML>

<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo $config["html_root"] . "/rs/css/pc/service_status.css";?>" />
</head>

<body>
	<section>
		<h3>Estado del servicio</h3>

		<?php
			// check named service:
			exec ("ps aux | grep named | grep -v grep | wc -l", $out, $return);
			if (($return == 0) && ($out[0] >= 1)) { $named_ok  = 1; }

			
			if ($named_ok) {
				exec ("ps axo pcpu,pmem,command | grep named | grep -v grep | awk 'BEGIN {sum=0}{sum+=$1}{print sum}'"
					,$cpu_usage
					,$return);

				exec ("ps axo pcpu,pmem,command | grep named | grep -v grep | awk 'BEGIN {sum=0}{sum+=$2}{print sum}'"
					,$ram_usage
					,$return);

				exec ("tail -n 15 /var/named/data/named.run 2>&1"
					,$log_output
					,$return);

				exec ("tail -n 15 /var/named/data/named.security 2>&1"
					,$security_log_output
					,$return);

				exec ("du -c -D -s -h /var/named/data | grep -i total | awk '{print $1}'"
					,$log_size
					,$return);

				exec ("rndc status 2>&1", $status_output, $return);

				echo "<p>Volcado de estado del servicio:</p><pre>";
				foreach ($status_output as $line) {
					echo $line . "\n";
				}
				echo "</pre>";

				echo "<p>El estado de Bind es correcto con " . $out[0] . " instancia(s) activa(s)</p>";
				echo sprintf("<p>Uso de CPU: %.02f %%</p>", $cpu_usage[0]);
				echo sprintf("<p>Uso de RAM: %.02f %%</p>", $ram_usage[0]);
				echo "<br><br>";

				echo "<p>Hay un total de " . $log_size[0] . "B en logs</p>";
				echo "<p>Informaci&oacute;n del log:</p><pre>";
				foreach ($log_output as $line){
					echo  $line . "\n";
				}
				echo "</pre>";
				echo "<br>";
				echo "<p>Informaci&oacute;n del log de seguridad:</p>";
				echo "<pre>";
				foreach ($security_log_output as $line){
					echo  $line . "\n";
				}
				echo "</pre>";

			}
			else {
				echo "<p>Bind est&aacute; detenido. No hay ninguna instancia activa</p>";
			}
		?>
	</section>
</body>

</html>