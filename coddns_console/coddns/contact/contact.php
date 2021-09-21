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

require_once __DIR__ . "/../include/config.php";
require_once __DIR__ . "/../lib/db.php";
require_once __DIR__ . "/../include/functions_util.php";
require_once __DIR__ . "/../lib/coduser.php";

if (! defined("_VALID_ACCESS")) { // Avoid direct access
    header("Location: " . $config["html_root"] . "/");
    exit(1);
}

try {
    $auth_level_required = get_required_auth_level('', 'contact', '');
    $user = new CODUser();
    $user->check_auth_level($auth_level_required);
} catch (Exception $e) {
    echo $e->getMessage();
    exit(1);
}

if (isset($_POST["name"])) {
    $name   = $_POST["name"];
}
if (isset($_POST["email"])) {
    $email   = $_POST["email"];
}
if (isset($_POST["tel"])) {
    $tel   = $_POST["tel"];
}
if (isset($_POST["mesage"])) {
    $mesage   = $_POST["mesage"];
}


?>
<!DOCTYPE HTML>

<html>
<head>
    <link rel="stylesheet" type="text/css" href="<?php echo $config["html_root"] . "/rs/css/" . $config["html_view"]; ?>/contact.css">
</head>

<body>
    <section>
        <h2>Send a message to development team</h2>

<?php
if ((!isset($config["slack_url"])) || ($config["slack_url"] == '')) {
    ?>
    <p>There's no <i>slack_url</i> defined in the configuration.</p><p>Please set it.</p>
    <?php
} elseif (isset($mesage)
    && (    isset($name)
    ||  isset($email)
    ||  isset($tel))
) {   // FORM COMPLETED
    isset($name) or $name   = "Anónimo";
    isset($email) or $email = "No definido";
    isset($tel) or $tel     = "No definido";

    if (isset($_POST["email"])) {
        $email   = $_POST["email"];
    }
    if (isset($_POST["tel"])) {
        $tel   = $_POST["tel"];
    }
    if (isset($_POST["mesage"])) {
        $mesage   = $_POST["mesage"];
    }


    ?>
    <p>Your message:</p>
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

    exec("curl --data \"$gmsg\" \"" . $config["slack_url"] . "\"", $service_output, $return);
} else {   // DISPLAY FORM
    ?>
        <form action="#" method="POST" onsubmit="">
            <ul>
                <li>
                    <label>Name</label><input name="name" type="text"/>
                </li>
                <li>
                    <label>Email</label><input name="email" type="email"/>
                </li>
                <li>
                    <label>Phone</label><input name="tel" type="text" pattern="(\+[0-9]{2}){0,1}[0-9]{9}" title="Please provide a valid phone number"/>
                </li>
            </ul>
            <textarea id="mesage" name="mesage" placeholder="Write your message here..."></textarea>
            <ul>
                <li>
                    <input type="submit" value="Send" />
                </li>
            </ul>
        </form>

    <?php
}
?>

    </section>
</body>
</html>
