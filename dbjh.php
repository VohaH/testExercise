<html>
<head>
<meta http-equiv="Content-type" content="text/css; charset=utf-8">
<title></title>
</head>
<body>

<?php

$mysqli = mysqli_connect("localhost:3306","root" ,"2894264Om" , "test");
if (!$mysqli) {
    echo('Ошибка при подключении: ' . mysqli_connect_error());
}

?>
</body>
</html>