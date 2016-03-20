<?php


include "include/config.php";
include "lib/db.php";
?>
<head>
</head>
<body>
<form action="#" method="POST">

<input name="text" type="text" />
<input type="submit" value="generate"/>
<br>
<textarea width="500">
<?php 
if (isset ($_POST["text"])){
	$db = new DBClient($db_config);
	$db->connect();
	echo $db->prepare($_POST["text"],"text");
	$db->disconnect();
}
?>
</textarea>

</body>
</html>
