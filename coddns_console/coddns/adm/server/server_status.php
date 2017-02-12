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
require_once(__DIR__ . "/../../lib/coduser.php");

$auth_level_required = get_required_auth_level('adm','server','status');
$user = new CODUser();
$user->check_auth_level($auth_level_required);

$servername = secure_get("id");

if (!isset ($servername)){
	custom_die("Unauthorized to access this content.");
}

require_once(__DIR__ . "/../../include/functions_server.php");
require_once(__DIR__ . "/../../lib/sshclient.php");


// Retrieve server credentials
$server = get_server_data($db_config, $servername);

if ($server === false) {
	custom_die("No existen credenciales para acceder a este servidor.");
}


if (empty($server->tag)){
	echo "No hay servidores registrados con ese nombre.";
	return 0;
}

// initialize ssh client
$sshclient = new SSHClient($server);

$sshclient->connect();

// Check if we're connected & authenticated into the server
if (! $sshclient->is_authenticated()){
	echo "Datos de acceso no v&aacute;lidos";
	return 0;
}

$dbclient = new DBClient($db_config);

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
			$result = $sshclient->launch ("ps aux | grep named | grep -v grep | wc -l");

			if ( ($result[0] >= 1) && ($result[1] == 0) ) { $named_ok  = 1; }

			
			if ($named_ok) {
				$cpu_usage = $sshclient->launch ("ps axo pcpu,pmem,command | grep named | grep -v grep | awk 'BEGIN {sum=0}{sum+=$1}{print sum}'");

				$ram_usage = $sshclient->launch ("ps axo pcpu,pmem,command | grep named | grep -v grep | awk 'BEGIN {sum=0}{sum+=$2}{print sum}'");

				$log_output = $sshclient->launch ("tail -n 15 /var/named/data/named.run 2>&1");

				$security_log_output = $sshclient->launch ("tail -n 15 /var/named/data/named.security 2>&1");

				$log_size = $sshclient->launch ("du -c -D -s -h /var/named/data | grep -i total | awk '{print $1}'");

				$status_output = $sshclient->launch ("rndc status 2>&1");

				echo "<p>Volcado de estado del servicio:</p><pre>";
				echo $status_output[0];
				echo "</pre>";

				echo "<p>El estado de Bind es correcto con " . $result[0] . " instancia(s) activa(s)</p>";
				echo sprintf("<p>Uso de CPU: %.02f %%</p>", $cpu_usage[0]);
				echo sprintf("<p>Uso de RAM: %.02f %%</p>", $ram_usage[0]);
				echo "<br><br>";

				echo "<p>Hay un total de " . $log_size[0] . "B en logs</p>";
				echo "<p>Informaci&oacute;n del log:</p><pre>";
				echo $log_output[0];
				echo "</pre>";
				echo "<br>";
				echo "<p>Informaci&oacute;n del log de seguridad:</p>";
				echo "<pre>";
				echo $security_log_output[0];
				echo "</pre>";

			}
			else {
				echo "<p>Bind est&aacute; detenido. No hay ninguna instancia activa</p>";
			}
		?>
	</section>
</body>

</html>
