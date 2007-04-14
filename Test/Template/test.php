<html>
<head>
 <title><?php echo $title; ?></title>
</head>
<body>
<h3><?php echo $foo; ?></h3>
<?php
foreach ($array as $key => $val) {
	echo "$key => $val<br>\n";
}
?>
</body>
</html>
