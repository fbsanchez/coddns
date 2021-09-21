<?php

require_once "lib/db.php";
require_once "include/config.php";
?>
<head>
</head>
<body>
<form action="#" method="GET">

<input name="text" type="text" />
<input type="submit" value="generate"/>
<br>
<textarea width="500">
<?php
if (isset($_GET["text"])) {
    $db = new DBClient($db_config);
    $db->connect();
    echo $db->prepare($_POST["text"], "text");
    $db->disconnect();
}
?>
</textarea>

<?php //phpinfo();
?>
</body>
</html>
