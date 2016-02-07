<?php
/* 
 * pub/index.php
 * Folder: pub
 *  Switches the received ID as manual page to be displayed
 */

include_once (dirname(__FILE__) . "/../include/config.php");
require_once (dirname(__FILE__) . "/../lib/ipv4.php");
require_once (dirname(__FILE__) . "/../lib/util.php");

?>

<!DOCTYPE html>
<html>

<body>
        <h1>Manuales</h1>

        <p>Navegue por el &iacute;ndice y elija el manual que desee</p>

        <div id="pub_index">
        </div>

</body>

</html>
