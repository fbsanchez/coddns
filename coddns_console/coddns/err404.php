<?php

require_once __DIR__ . "/include/config.php";

?>

<!DOCTYPE html>
<html>
<head>
<title>Not found!!</title>

</head>

<body>

<section class="main_section" style="text-align: center;">
  <p><span style="font-size: 8em;">404</span>
  <span style="font-size: 4em;"> page not found</span></p>
  <img  style="display:block; margin: 25px auto;" src="<?php echo $config["html_root"]?>/rs/img/404.jpg" alt="not-found"/>
  <p><span style="font-size: 2em;"> We cannot find the page you're looking for</span></p>
  <a style="text-decoration: none;" href ="<?php echo $config["html_root"];?>/">Go to main page</a>
</section>

</body>
</html>

