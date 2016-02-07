<?php
// CODDNS INSTALLER


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
if (($return == 0) && ($out[0] >= 1)) { $dnsmgr_ok  = 1; }

// check nmap is present
exec ("which nmap | wc -l", $out, $return);
if (($return == 0) && ($out[0] >= 1)) { $nmap_ok  = 1; }


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
	<header>
		<img src="rs/img/coddns_225.png" alt="logo"/>
		<p style="float: right;margin: 17px 1em 0px 0px;color: #fff;font-size: 0.72em;">Versi&oacute;n 2.0</p>
	</header>
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
						<label>Servidor:</label><input name="dbh" type="text"/>
					</li>
					<li>
						<label>Puerto:</label><input id="dbp" name="dbp" type="number" value="3306"/>
					</li>
					<li>
						<label>Base de datos:</label><input name="db" type="text"/>
					</li>
					<li id="schema" style="padding:0;max-height:0;overflow:hidden;">
						<label>Esquema:</label><input name="sch" type="text"/>
					</li>
					<li>
						<label>Usuario:</label><input name="user" type="text"/>
					</li>
					<li>
						<label>Contrase&ntilde;a:</label><input name="pass" type="password"/>
					</li>
					<li>
						<input type="submit" value="Instalar"/>
					</li>
				</ul>
			</form>
		</div>
	</article>
</section>

</body>

</hmtl>
