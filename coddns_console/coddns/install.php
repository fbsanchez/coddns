<?php
// CODDNS INSTALLER
require_once(dirname(__FILE__) . "/lib/db.php");
require_once(dirname(__FILE__) . "/lib/util.php");

function check_lib($item){
	if (!extension_loaded($item)) {
		return FALSE;
	}
	return TRUE;
}

function check_item($item){
	if ($item == 1)
		echo "ok";
	else
		echo "fail";
}

function print_header($phase) {
	?>
	<header>
		<img src="rs/img/coddns_225.png" alt="logo"/>
		<p style="float: right;margin: 17px 1em 0px 0px;color: #fff;font-size: 0.72em;">Fase <?php echo $phase;?>/3</p>
	</header>
	<?php
}

?>

<!DOCTYPE HTML>
<hmtl>
<head>
<title>CODDNS Installer - Integrated management of name resolution services</title>
<link href='http://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
<link rel="icon" href="rs/img/coddns.ico">
<link rel="stylesheet" type="text/css" href="rs/css/pc/install.css">
<script type="text/javascript">
function toggle(id){
	if (id.style['max-height'] == '') {
		id.style['max-height'] = '1000px';
	}
	else {
		id.style['max-height'] = '';
	}
}
function update_data_form(){
	if (engine.value==""){
		data_form.style['max-height'] = '0px';
		return false;
	}
	else if (engine.value=="mysql") {
		schema.style['max-height']='0px';
		schema.style['padding']='0px';
		dbp.value=3306;
	}
	else if (engine.value=="postgresql") {
		schema.style['padding']='2px';
		toggle(schema);
		dbp.value=5432;
	}
	data_form.style['max-height'] = '1000px';
}
</script>
</head>

<body>
<section id="main_wizard">

<?php

if(    (!isset ($_POST["engine"]))
	|| (!isset ($_POST["dbroot"]))
	|| (!isset ($_POST["dbrpass"]))
	|| (!isset ($_POST["dbuser"]))
	|| (!isset ($_POST["dbpass"]))
	|| (!isset ($_POST["dbhost"]))
	|| (!isset ($_POST["dbport"]))
	|| (!isset ($_POST["dbname"]))
	) { // NO PHASE 2 expected values received, can be at 1 or 3
	$phase = 3;
	if(    (!isset($_POST["html_root"]))
		|| (!isset($_POST["admuser"]))
		|| (!isset($_POST["admpass"]))
	) { // NO PHASE 3 expected values received, I must be on 1
		$phase = 1;
	}
}
else { // PHASE 2 expected values received: I should be on 2
	$phase = 2;
}


if ($phase == 1) {
// TESTS BEGIN

$named_ok  = 0;
$dnsmgr_ok = 0;
$mysqli_ok = 0;
$pgsql_ok  = 0;
$nmap_ok   = 0;
$global_ok = 0;

// check named service:
exec ("ps aux | grep named | grep -v grep | wc -l", $out, $return);
if (($return == 0) && ($out[0] >= 1)) { $named_ok  = 1; }

// check ddns_manager is present
exec ("which dnsmgr | wc -l", $out, $return);
if (($return == 0) && ($out[1] >= 1)) { $dnsmgr_ok  = 1; }

// check nmap is present
exec ("which nmap | wc -l", $out, $return);
if (($return == 0) && ($out[2] >= 1)) { $nmap_ok  = 1; }


// check php extensions
if (check_lib("mysqli"))           { $mysqli_ok = 1; }
if (check_lib("pgsql"))            { $pgsql_ok  = 1; }

if ($named_ok+$dnsmgr_ok+$mysqli_ok+$pgsql_ok >= 3){
	$global_ok = 1;
}

// TESTS END

//$service_requeriments = $named_ok + $dnsmgr_ok;
//$global_requeriments = $service_requeriments + 
?>
	<?php print_header(1) ?>
	<article>
		<div>
			<h1>Bienvenido a CODDNS</h1>
			<p>Gracias por elegir CODDNS como su sistema de gesti&oacute;n integral de servicios de resoluci&oacute;n de nombres de dominio.</p>
			<br />
			<p>Por favor, antes de continuar, verifique que se cumplen todos los requisitos en negrita, son <b>imprescindibles</b>.</p>
		</div>
		<div class="t_label" onclick="toggle(requeriments);">
			<div class="status <?php echo check_item($global_ok);?>">&nbsp;</div>Requisitos
		</div>
		<div class="tab" id="requeriments">
			<i>Software y servicios</i>
			<ul>
				<li>
					<div class="status <?php check_item($named_ok);?>">&nbsp;</div>
						<b>Bind - Servicio DNS</b>
				</li>
				<li>
					<div class="status <?php check_item($dnsmgr_ok);?>">&nbsp;</div>
						<b>DNS manager script</b>
				</li>
				<li>
					<div class="status ok">&nbsp;</div> <b>Servidor web</b>
				</li>
				<li>
					<div class="status ok">&nbsp;</div> <b>PHP</b>
				</li>
			</ul>
			<b><i>Conectores a bases de datos</i></b> <span style="font-size:0.65em;">(al menos uno)</span>
			<ul>
				<li>
					<div class="status <?php check_item($mysqli_ok);?>">&nbsp;</div> PHP MySQLi
				</li>
				<li>
					<div class="status <?php check_item($pgsql_ok);?>">&nbsp;</div> PHP PostgreSQL
				</li>
			</ul>
			<i>Herramientas</i> <span style="font-size:0.65em;">(opcional)</span>
			<ul>
				<li>
					<div class="status <?php check_item($nmap_ok);?>">&nbsp;</div> nmap
				</li>
			</ul>
		</div>
		<div class="t_label" onclick="toggle(data);">
			<div class="status">&nbsp;</div>Datos
		</div>
		<div class="tab" id="data">
			<form id="mysql" name="dbdata" method="POST">
				<label>Motor de la base de datos:</label>
				<select id="engine" name="engine" onchange="update_data_form();">
					<option value="" selected>Seleccione</option>
					<?php
					if ($mysqli_ok) {
					?>
					<option value="mysql">MySQL</option>
					<?php
					}
					if ($pgsql_ok) {
					?>
					<option value="postgresql">PostgreSQL</option>
					<?php
					}
					?>
				</select>
				<ul id="data_form" style="max-height: 0px;overflow:hidden;transition:max-height 1s 0s;">
					<li>
						<label>Servidor:</label><input name="dbhost" type="text" value="localhost"/>
					</li>
					<li>
						<label>Puerto:</label><input id="dbp" name="dbport" type="number" value="3306"/>
					</li>
					<li>
						<label>Base de datos:</label><input name="dbname" type="text" value="coddns"/>
					</li>
					<li id="schema" style="padding:0;max-height:0;overflow:hidden;">
						<label>Esquema:</label><input name="schema" type="text" value="dbnsp"/>
					</li>
					<li>
						<label>Usuario:</label><input name="dbroot" type="text" value="root"/>
					</li>
					<li>
						<label>Contrase&ntilde;a:</label><input name="dbrpass" type="password"/>
					</li>
					<li>
						<label>Nuevo usuario:</label><input name="dbuser" type="text" value="coddns"/>
					</li>
					<li>
						<label>Nueva contrase&ntilde;a:</label><input name="dbpass" type="password"/>
					</li>
					<li>
						<label>Realizar instalaci&oacute;n limpia:</label><input name="dbdrop" type="checkbox" checked/>
					</li>
					<li>
						<input type="submit" value="Instalar"/>
					</li>
				</ul>
			</form>
		</div>
	</article>
<?php
}
elseif ($phase == 2) {
?>
	<?php
	print_header(2);

	$engine  = DBClient::prepare($_POST["engine"],"insecure_text");
	$dbroot  = DBClient::prepare($_POST["dbroot"],"insecure_text");
	$dbrpass = $_POST["dbrpass"];
	$dbuser  = DBClient::prepare($_POST["dbuser"],"insecure_text");
	$dbpass  = $_POST["dbpass"];
	$dbname  = DBClient::prepare($_POST["dbname"],"insecure_text");
	$dbhost  = DBClient::prepare($_POST["dbhost"],"insecure_text");
	$dbport  = DBClient::prepare($_POST["dbport"],"number");
	$schema  = DBClient::prepare($_POST["schema"],"insecure_text");
	$dbdrop  = $_POST["dbdrop"];


	// Create temp configuration
	/*
	 * Database configuration ~ Temporary based on root user
	 */
	$db_config = array("engine"  =>"$engine", // Could be mysql or postgresql
	                   "username"=>"$dbroot",
	                   "password"=>"$dbrpass",
	                   "hostname"=>"$dbhost",
	                   "port"    =>"$dbport",
	                   "name"    =>"",
	                   "schema"  =>"");

	switch ($engine) {
		case "mysql":
			$sql_file = dirname(__FILE__) . "/coddns_sql/coddns_mysql.sql";
			break;
		case "postgresql":
			$sql_file = dirname(__FILE__) . "/coddns_sql/coddns_pgsql.sql";
			break;
		default:
			$sql_file = "";
			die ("Error, please follow the wizard.");
			break;
	}

	file_exists($sql_file) or die ("Scrips SQL no encontrados.");
	

	// Initialize flags
	$sql_connection_ok  = 0;
	$sql_process_ok     = 0;
	$drop_database_ok   = 0;
	$create_database_ok = 0;
	$grant_user_ok      = 0;

	// Connect to SQL
	$dbclient = new DBClient($db_config);

	if($dbclient->connect()) {
		$sql_connection_ok = 1;
	}
	if ($sql_connection_ok == 1){
		$engine  = $dbclient->prepare($_POST["engine"],"text");
		$dbroot  = $dbclient->prepare($_POST["dbroot"],"text");
		$dbrpass = $_POST["dbrpass"];
		$dbuser  = $dbclient->prepare($_POST["dbuser"],"text");
		$dbpass  = $_POST["dbpass"];
		$dbname  = $dbclient->prepare($_POST["dbname"],"text");
		$dbhost  = $dbclient->prepare($_POST["dbhost"],"text");
		$dbport  = $dbclient->prepare($_POST["dbport"],"number");
		$schema  = $dbclient->prepare($_POST["schema"],"text");
		$dbdrop  = $_POST["dbdrop"];

		// DROP DATABASE
		if ("$dbdrop" == "on"){
			$q = "drop database if exists $dbname;";
			$r = $dbclient->exeq($q);
			$drop_message = $dbclient->lq_error();

			if($r) {
				$drop_database_ok = 1;
			}
		}

		// CREATE NEW DATABASE
		$q = "create database if not exists $dbname;";
		$r = $dbclient->exeq($q);
		if($r){
			$create_database_ok = 1;
		}
		else {
			$grant_message = $dbclient->lq_error();
		}

		// GRANT PRIVILEGES
		if ($create_database_ok == 1){
			switch ($engine){
				case "mysql":
					$q = "grant all privileges on $dbname.* to $dbuser@$dbhost identified by \"$dbpass\";";
				break;
				case "postgresql":
					$q  = "create user $dbuser;";
					$q .= "grant all on database $dbname to $dbuser;";
					$q .= "grant all on schema $schema to $dbuser;";
					$q .= "grant all on all tables in schema $schema to $dbuser;";
					$q .= "grant all on all sequences in schema $schema to $dbuser;";
					$q .= "alter user $dbuser with password \'$dbpass\';";
				break;
				default:
					die("Error, please follow the wizard.");
				break;
			}
			
			$r = $dbclient->exeq($q);
			$create_message = $dbclient->lq_error();
			if($r){
				$grant_user_ok = 1;
			}
			else {
				$grant_message = $dbclient->lq_error();
			}
		}	

		// PROCESS SQL SCRIPT
		if ($grant_user_ok == 1){
			switch ($engine){
				case "mysql":
					$command = "mysql -u $dbuser -p'$dbpass' -h $dbhost $dbname < $sql_file";
					exec ($command . " 2>&1", $sql_file_exec, $return);
					break;
				case "postgresql":
					$_ENV{"PGPASSWORD"} = "\'$dbpass\'";
					$command = "pgsql -U $dbuser -w -h $dbhost -d $dbname -f $sql_file";
					exec ($command . " 2>&1"	, $sql_file_exec, $return);
					break;
				default:
					die("Error, please follow the wizard.");
				break;
			}

			if($return == 0){
				$sql_process_ok = 1;
			}
			else {
				$sql_script_message = $sql_file_exec;
			}
		}
		$dbclient->disconnect();
	}

	?>
	<article>
		<div>
			<p>Resultados del proceso de instalaci&oacute;n:</p>
			
			<br />
			<ul style="width: auto;">
				<li>
					<div class="status <?php check_item($sql_connection_ok);?>">&nbsp;</div> Conexi&oacute;n con el servidor
			<?php
				if(isset($connection_message)) {
					echo "<div onclick='toggle(this);' class='err'>Error: $drop_message</div>";
				}
			?>
				</li>
			<?php
				if ("$dbdrop" == "on") {
			?>
				<li>
					<div class="status <?php check_item($drop_database_ok);?>">&nbsp;</div> Eliminado de base de datos <?php echo $dbname;?> previa
			<?php
					if(isset($drop_message)) {
						echo "<div onclick='toggle(this);' class='err'>Error: $drop_message</div>";
					}
			?>
				</li>
			<?php
				}
			?>
				<li>
					<div class="status <?php check_item($create_database_ok);?>">&nbsp;</div> Creaci&oacute;n de base de datos <?php echo $dbname;?>
			<?php
				if(isset($create_message)) {
					echo "<div onclick='toggle(this);' class='err'>Error: $create_message</div>";
				}
			?>
				</li>
				<li>
					<div class="status <?php check_item($grant_user_ok);?>">&nbsp;</div> Asignaci&oacute;n de permisos a <?php echo $dbuser; ?>
			<?php
				if(isset($grant_message)) {
					echo "<div onclick='toggle(this);' class='err'>Error: $grant_message</div>";
				}
			?>
				</li>
				<li>
					<div class="status <?php check_item($sql_process_ok);?>">&nbsp;</div> Procesado de scripts de despliegue
			<?php
				if(isset($sql_script_message)) {
					echo "<div onclick='toggle(this);' class='err'>Error: ";
					echo "<pre>";
					var_dump($sql_script_message);
					echo "</pre>";
					echo "</div>";
				}
			?>
				</li>
			</ul>
		</div>
		<?php
			if ($sql_process_ok == 1) {
		?>
		<div class="t_label" onclick="toggle(data);">
			<div class="status">&nbsp;</div>Configuraci&oacute;n final del sitio:
		</div>
		<div class="tab" style="max-height: 1000px" id="data">
			<form id="mysql" name="dbdata" method="POST">
				<ul>
					<li>
						<label>Dominio principal:</label><input name="domain" type="text" value="coddns.lan"/>
					</li>
					<li>
						<label>Directorio HTML principal</label><input name="user" type="text" value="<?php echo preg_replace("/install\.php$/","",$_SERVER[REQUEST_URI]);?>"/>
					</li>
					<li>
						<label>Cuenta de administraci&oacute;n:</label><input name="user" type="email"/>
					</li>
					<li>
						<label>Contrase&ntilde;a:</label><input id="dbp" name="pass" type="password"/>
					</li>
					<li>
						<label>Semilla hash:</label><input name="hash" type="text" value="<?php echo base64_encode(time());?>"/>
					</li>
					<li>
						<input type="submit" value="Completar instalaci&oacute;n"/>
					</li>
				</ul>
			</form>
		</div>


		<?php
		}
		?>
	</article>

</section>
<?php
}
elseif ($phase == 3){
?>

<?php
}
else
	die ("Please follow the wizard");
?>
</section>
</body>

</hmtl>
