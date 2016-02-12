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

// CODDNS INSTALLER

defined ("MIN_USER_LENGTH") or define ("MIN_USER_LENGTH", 4);
defined ("MIN_PASS_LENGTH") or define ("MIN_PASS_LENGTH", 4);
defined ("MIN_DB_LENGTH") or define ("MIN_DB_LENGTH", 2);

require_once(dirname(__FILE__) . "/lib/db.php");

function isOverHTTPS(){
    if (isset($_SERVER["HTTPS"]) && $_SERVER['SERVER_PORT'] == '443')
        return true;
    return false;
}

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

<?php
    if(isOverHTTPS()) {
?>
<link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
<?php
} else {
?>
<link href='http://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
<?php
}
?>
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
	|| ( strlen($_POST["dbroot"]) < MIN_USER_LENGTH)
	|| ( strlen($_POST["dbname"]) < MIN_DB_LENGTH )
	) { // NO PHASE 2 expected values received, can be at 1 or 3
	$phase = 3;
	if(    (!isset($_POST["html_root"]))
		|| (!isset($_POST["user"]))
		|| (!isset($_POST["pass"]))
		|| (!isset($_POST["domain"]))
		|| (!isset($_POST["hash"]))
	) { // NO PHASE 3 expected values received, I must be on 1
		$phase = 1;
	}
}
else { // PHASE 2 expected values received: I should be on 2
	$phase = 2;
}


if ($phase == 1) {
// TESTS BEGIN

// First of all unset all active sessions
session_start();
session_destroy();
session_write_close();


$named_ok  = 0;
$dnsmgr_ok = 0;
$mysqli_ok = 0;
$pgsql_ok  = 0;
$nmap_ok   = 0;
$global_ok = 0;
$writable_config_ok = 0;

// check named service:
exec ("/etc/init.d/named status | wc -l", $out, $return);
if (($return == 0) && ($out[0] >= 1)) { $named_ok  = 1; }

// check ddns_manager is present
exec ("which dnsmgr | wc -l", $out, $return);
if (($return == 0) && ($out[1] >= 1)) { $dnsmgr_ok  = 1; }

// check nmap is present
exec ("which nmap | wc -l", $out, $return);
if (($return == 0) && ($out[2] >= 1)) { $nmap_ok  = 1; }

// Check if configuration directory is writable
if (is_writable(dirname(__FILE__) . "/include")){
	$writable_config_ok = 1;
}

// check php extensions
if (check_lib("mysqli"))           { $mysqli_ok = 1; }
if (check_lib("pgsql"))            { $pgsql_ok  = 1; }

if ($named_ok+$dnsmgr_ok+$writable_config_ok == 3){
	if ($mysqli_ok+$pgsql_ok >= 1){
		$global_ok = 1;
	}
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
		<div class="tab" id="requeriments" <?php if ($global_ok !=1) echo "style='max-height:1000px;'";?> >
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
				<li>
					<div class="status <?php check_item($writable_config_ok);?>">&nbsp;</div>
						<b>Permisos de escritura sobre el directorio de configuraci&oacute;n</b>
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
		<?php
			// DO NOT SHOW FORM TO PHASE 2 if gobal checks are not completed
			if($global_ok == 1) {
		?>
		<div class="t_label" onclick="toggle(data);">
			<div class="status">&nbsp;</div>Datos
		</div>
		<div class="tab" id="data" <?php if ($global_ok ==1) echo "style='max-height:1000px;'";?>>
			<form id="mysql" name="dbdata" method="POST" onsubmit="dbrpass.value=btoa(dbrpass.value);dbpass.value=btoa(dbpass.value);">
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
				<script type="text/javascript">
				function check_dbhost(val){
					if(   (val.value == "localhost")
						||(val.value == "127.0.0.1")) {
						myip_li.style["display"] = "none";
						myip.required = false;
					}
					else {
						myip_li.style["display"] = "block";
						myip.required = true;
					}
				}
				</script>
					<li>
						<label>Servidor:</label><input name="dbhost" type="text" value="localhost" onchange="check_dbhost(this);"/>
					</li>
					<li id="myip_li" style="display: none;">
						<label>IP origen:</label><input id="myip" name="myip" type="text"/>
					</li>
					<li>
						<label>Puerto:</label><input id="dbp" name="dbport" type="number" value="3306"/>
					</li>
					<li>
						<label>Base de datos:</label><input name="dbname" type="text" value="coddns"/>
					</li>
					<li id="schema" style="padding:0;max-height:0;overflow:hidden;">
						<label>Esquema:</label><input name="schema" type="text"/>
					</li>
					<li>
						<label>Usuario:</label><input name="dbroot" type="text" value="root"/>
					</li>
					<li>
						<label>Contrase&ntilde;a:</label><input id="dbrpass" name="dbrpass" type="password"/>
					</li>
					<li>
						<label>Nuevo usuario:</label><input name="dbuser" type="text" value="coddns"/>
					</li>
					<li>
						<label>Nueva contrase&ntilde;a:</label><input id="dbpass" name="dbpass" type="password"/>
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
		<?php
	}
		?>
	</article>
<?php
}
elseif ($phase == 2) {
	print_header(2);

	$engine  = DBClient::prepare($_POST["engine"],"insecure_text");
	$dbroot  = DBClient::prepare($_POST["dbroot"],"insecure_text");
	$dbrpass = base64_decode($_POST["dbrpass"]);
	$dbuser  = DBClient::prepare($_POST["dbuser"],"insecure_text");
	$dbpass  = base64_decode($_POST["dbpass"]);
	$dbname  = DBClient::prepare($_POST["dbname"],"insecure_text");
	$dbhost  = DBClient::prepare($_POST["dbhost"],"insecure_text");
	$dbport  = DBClient::prepare($_POST["dbport"],"number");
	$schema  = DBClient::prepare($_POST["schema"],"insecure_text");
	$dbdrop  = $_POST["dbdrop"];

	// remove spaces from dbname
	$dbname = preg_replace('/\s+/', '', $dbname);

	// if no dbuser is provided, use dbroot as well
	if ("$dbuser" == ""){
		$dbuser = $dbroot;
	}


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
		$dbrpass = base64_decode($_POST["dbrpass"]);
		$dbuser  = $dbclient->prepare($_POST["dbuser"],"text");
		$dbpass  = base64_decode($_POST["dbpass"]);
		$dbname  = $dbclient->prepare($_POST["dbname"],"text");
		$dbhost  = strtolower($dbclient->prepare($_POST["dbhost"],"text"));
		$myip    = strtolower($dbclient->prepare($_POST["myip"],"text"));
		$dbport  = $dbclient->prepare($_POST["dbport"],"number");
		$schema  = $dbclient->prepare($_POST["schema"],"text");
		$dbdrop  = $_POST["dbdrop"];

		if (!isset ($dbport)) {
			switch ($engine){
				case "mysql":$dbport=3306;break;
				case "postgresql":$dbport=5432;break;
				default:die("Please use the wizard.");break;
			}
		}

		if (   (!isset ($myip))
			|| ("$myip" == "")){
			if (   ($dbhost == "127.0.0.1")
				|| ($dbhost == "localhost")) {
				$myip = $dbhost;
			}
			else {
				$myip = $_SERVER["SERVER_ADDR"];
			}
		}

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

			if ($dbuser == $dbroot) {
				// dbroot user also has grants ~ user password ignored.
				$grant_user_ok = 1;
			}
			else {
				switch ($engine){
					case "mysql":
						$q = "grant all privileges on $dbname.* to $dbuser@" . $myip . " identified by '$dbpass';";
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
		}	

		// PROCESS SQL SCRIPT
		if ($grant_user_ok == 1){
			switch ($engine){
				case "mysql":
					$command = "mysql -u $dbuser -p'$dbpass' -h $dbhost -P $dbport $dbname < $sql_file";
					exec ($command . " 2>&1", $sql_file_exec, $return);
					break;
				case "postgresql":
					$_ENV{"PGPASSWORD"} = "$dbpass";
					$command = "pgsql -U $dbuser -w -h $dbhost -p $dbport -d $dbname -f $sql_file";
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
				$sql_script_message = "";
				foreach ($sql_file_exec as $line){
					$sql_script_message .= $line;
				}
			}
		}
		$dbclient->disconnect();

		if ($sql_process_ok == 1) {
			// Succesfully installed, store config to SESSION
			$db_config = array("engine"  =>"$engine", // Could be mysql or postgresql
                   "username"=>"$dbuser",
                   "password"=>"$dbpass",
                   "hostname"=>"$dbhost",
                   "port"    =>"$dbport",
                   "name"    =>"$dbname",
                   "schema"  =>"$schema");
			session_start();
			$_SESSION["config"] = $db_config;
			session_write_close();
		}
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
			<form id="mysql" name="finalcfg" method="POST" onsubmit="pass.value=btoa(pass.value);">
				<ul>
					<li>
						<label>Dominio principal:</label><input name="domain" type="text" value="coddns.lan"/>
					</li>
					<li>
						<label>Directorio HTML principal</label><input name="html_root" type="text" value="<?php echo preg_replace("/\/install\.php$/","",$_SERVER['REQUEST_URI']);?>"/>
					</li>
					<li>
						<label>Cuenta de administraci&oacute;n:</label><input name="user" type="email"/>
					</li>
					<li>
						<label>Contrase&ntilde;a:</label><input id="pass" name="pass" type="password"/>
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
<?php
}
elseif ($phase == 3){
	session_start();
	$config = $_SESSION["config"];
	session_write_close();

	print_header(3);

	$html_root = $_POST["html_root"];
	$user      = $_POST["user"];
	$rq_pass   = base64_decode($_POST["pass"]);
	$domain    = $_POST["domain"];
	$salt      = $_POST["hash"];



	// BUILD FINAL CONFIGURATION FILE
	$str_config  = "<?php\n";
	$str_config .= "\n";
	$str_config .= "/*\n";
	$str_config .= " * Database configuration\n";
	$str_config .= " */\n";
	$str_config .= "\$db_config = array(\"engine\"  =>\"" . $config["engine"] . "\", // Could be mysql or postgresql\n";
	$str_config .= "                   \"username\"=>\"" . $config["username"] . "\",\n";
	$str_config .= "                   \"password\"=>'" . $config["password"] . "',\n";
	$str_config .= "                   \"hostname\"=>\"" . $config["hostname"] . "\",\n";
	$str_config .= "                   \"port\"    =>\"" . $config["port"] . "\",\n";
	$str_config .= "                   \"name\"    =>\"" . $config["name"] . "\",\n";
	$str_config .= "                   \"schema\"  =>\"" . $config["schema"] . "\");\n";
	$str_config .= "\n";
	$str_config .= "// domain name: FQDN base for the system\n";
	$str_config .= "// html_root: if you want to access http://yousite.yourdomain/coddns\n";
	$str_config .= "//            set it to /coddns, is the nav location\n";
	$str_config .= "\$config = array (\"domainname\" => \"" . $domain . "\",\n";
	$str_config .= "                  \"html_root\"  => \"" . $html_root . "\",\n";
	$str_config .= "                  \"salt\"       => \"" . $salt . "\",\n";
	$str_config .= "                  \"db_config\"  => \$db_config);\n";
	$str_config .= "\n";
	$str_config .= "defined (\"MIN_USER_LENGTH\") or define (\"MIN_USER_LENGTH\", 4);\n";
	$str_config .= "defined (\"MIN_PASS_LENGTH\") or define (\"MIN_PASS_LENGTH\", 4);\n";
	$str_config .= "defined (\"MIN_HOST_LENGTH\") or define (\"MIN_HOST_LENGTH\", 1);\n";
	$str_config .= "defined (\"MAX_HOST_LENGTH\") or define (\"MAX_HOST_LENGTH\", 200);\n";
	$str_config .= "\n";
	$str_config .= "?>\n";

	$file = dirname(__FILE__) . "/include/config.php";

	if (! is_writable(dirname(__FILE__) . "/include")){
		die("El directorio " . dirname(__FILE__) . "/include" . "no es accesible por el instalador, por favor verifique los requisitos");
	}
	file_put_contents($file, $str_config);


	// FINAL STEP - create admin user using the configuration file
	include_once (dirname(__FILE__) . "/include/config.php");
	require_once (dirname(__FILE__) . "/lib/ipv4.php");

	$dbclient = new DBClient($db_config);

	$user = $dbclient->prepare($_POST["user"], "email");
	$pass = hash ("sha512",$salt . $rq_pass);

	$dbclient->connect() or die ("<div onclick='toggle(this);' class='err'>Error: " . $dbclient->lq_error() . "</div>");

	$q = "Select * from " . $db_config["schema"] . ".users where lower(mail)=lower('" . $user . "');";
	$dbclient->exeq($q) or die ("<div onclick='toggle(this);' class='err'>Error: " . $dbclient->lq_error() . "</div>");
	if ($dbclient->lq_nresults() == 0){ // ADD NEW USER
	    
	    // Create administrator user
	    $q = "insert into " . $db_config["schema"] . ".users (mail, pass, ip_last_login, first_login, rol) values (lower('" . $user . "'),'" . $pass . "', '" . _ip() . "', now(), (select id from roles where tag='admin'));";
	    $dbclient->exeq($q) or die ("<div onclick='toggle(this);' class='err'>Error: " . $dbclient->lq_error() . "</div>");
	    $oid = $dbclient->last_id();

	    // Add user to global group
	    $q = "insert into " . $db_config["schema"] . ".tusers_groups (oid,gid,admin) values ($oid,(select id from groups where tag='all'),1);";
	    $dbclient->exeq($q) or die ("<div onclick='toggle(this);' class='err'>Error: " . $dbclient->lq_error() . "</div>");

	    // Welcome mail to user
	    $text_sender               = "CODDNS";
		$email_sender              = "noreply@" . $config["domainname"];
		$text_mail_welcome_body    = "Hola!\n\n Gracias por instalar CODDNS,\nPuedes acceder a la herramienta desde el enlace siguiente:\n " . $_SERVER['HTTP_ORIGIN'] . $html_root . "\n";
		$text_mail_welcome_subject = "CODDNS Instalacion completada!";

	    $recipient = $user;                    //recipient
	    $mail_body = $text_mail_welcome_body;  //mail body
	    $subject = $text_mail_welcome_subject; //subject
	    $header = "From: " . $text_sender . " <" . $email_sender . ">\r\n"; //optional headerfields
	    mail($recipient, $subject, $mail_body, $header); //mail command :)
	}
	else {
	    die ("<div onclick='toggle(this);' class='err'>Error: Ese usuario ya existe</div>");
	    exit(1);
	}

	$dbclient->disconnect();

	// process login with new user.
	require_once (dirname(__FILE__) . "/lib/coduser.php");
	$objUser = new CODUser();
	if ($objUser->login($user, $rq_pass) == null ) {
		die ("Problem loading user, please rerun the installation process.");
	}
	// CONFIG WRITTEN
?>
	<article>
	<div style="width: 100px; height: 100px; margin: 15px auto;">
		<img src="<?php echo $html_root . "rs/img/ok.png"?>" alt="END" />
	</div>
	<p>El proceso de instalaci&oacute;n ha finalizado correctamente.</p>
	<br>
	<p><b>Datos de acceso</b> (pasar por encima de <i>"contrase&ntilde;a"</i> para visualizarla):</p>

	<ul style="font-size: 0.8em;">
		<li>Usuario: <?php echo $user;?></li>
		<li onmouseout="pass.style['display']='none';" onmouseover="pass.style['display']='inline-block';">Contrase&ntilde;a: <div id="pass" style="display:none;"><?php echo base64_decode($_POST["pass"]);?></div></li>
	</ul>
	<p>Haga click <a href="<?php echo $_SERVER['HTTP_ORIGIN'] . $html_root; ?>">aqu&iacute;</a> para acceder a la herramienta.</p>

	</article>

<?php
}
else
	die ("Please follow the wizard");
?>
</section>
</body>

</hmtl>

<?php

session_write_close();

?>
