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

require_once(__DIR__ . "/../include/config.php");
require_once(__DIR__ . "/../lib/db.php");
require_once(__DIR__ . "/../include/functions_util.php");
require_once(__DIR__ . "/../lib/coduser.php");

if (! defined("_VALID_ACCESS")) { // Avoid direct access
	header ("Location: " . $config["html_root"] . "/");
	exit (1);
}

$auth_level_required = get_required_auth_level('','contact','');
$user = new CODUser();
$user->check_auth_level($auth_level_required);

if (isset ($_POST["name"])){
	$name   = $_POST["name"];
}
if (isset ($_POST["email"])){
	$email   = $_POST["email"];
}
if (isset ($_POST["tel"])){
	$tel   = $_POST["tel"];
}
if (isset ($_POST["mesage"])){
	$mesage   = $_POST["mesage"];
}


?>
<!DOCTYPE HTML>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="rs/css/pc/contact.css">
</head>

<body>
	<section>
		<h2>Enviar un mensaje a los desarrolladores</h2>

<?php
if ((!isset ($config["slack_url"])) || ($config["slack_url"] == '')) {
?>
	<p>No se ha encontrado el token "slack_url" en la configuraci&oacute;n</p>
	<p>Por favor, ajuste el valor en ajustes.</p>
<?php
}elseif( isset($mesage)
	&& ( 
		isset($name)
		||  isset($email)
		||  isset($tel))) {   // FORM COMPLETED

	isset ($name) or $name   = "Anónimo";
	isset ($email) or $email = "No definido";
	isset ($tel) or $tel     = "No definido";

	if (isset ($_POST["email"])){
		$email   = $_POST["email"];
	}
	if (isset ($_POST["tel"])){
		$tel   = $_POST["tel"];
	}
	if (isset ($_POST["mesage"])){
		$mesage   = $_POST["mesage"];
	}


?>
	<p> Tu mensaje es:</p>
	<pre>
		<?php
		echo "Name:" . $name . "\n";
		echo "Tel:"  . $tel . "\n";
		echo "Email:" . $email . "\n";
		echo "Msg:" . $mesage . "\n";
		?>
	</pre>
	<?php


	// Send mesage with curl:

	$gmsg = "New message from: $name\nPhone: $tel\nEmail: $email\n" . $mesage . "\n";

	exec ("curl --data \"$gmsg\" \"" . $config["slack_url"] . "\"", $service_output, $return);



}
else {   // DISPLAY FORM
?>
		<form action="#" method="POST" onsubmit="">
			<ul>
				<li>
					<label>Nombre</label><input name="name" type="text"/>
				</li>
				<li>
					<label>Email</label><input name="email" type="email"/>
				</li>
				<li>
					<label>Telefono</label><input name="tel" type="text" pattern="(\+[0-9]{2}){0,1}[0-9]{9}" title="Introduce un n&uacute;mero de tel&eacute;fono v&aacute;lido"/>
				</li>
			</ul>
			<textarea id="mesage" name="mesage" placeholder="Escribe tu mensaje aqu&iacute;..."></textarea>
			<ul>
				<li>
					<input type="submit" value="Enviar" />
				</li>
			</ul>
		</form>

<?php 
}
?>

	</section>
</body>
</html>