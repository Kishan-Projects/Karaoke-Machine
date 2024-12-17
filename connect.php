<?php


$username = "z2047813";
$password = "2001Apr09";
	
try { 
	// PDO object
	$dsn = "mysql:host=courses;dbname=z2047813";
	$pdo = new PDO($dsn, $username, $password);
	$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
}
catch(PDOexception $e) {  
	echo "Connection to database failed: " . $e->getMessage();
	die();
}
?>
