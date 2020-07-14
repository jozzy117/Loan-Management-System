<?php
$hostname = 'localhost';
$username = 'root';
$password = '';
$databaseName = 'loanstar';

$conn = mysqli_connect($hostname, $username, $password, $databaseName);

 if (!$conn) {
 	die ("Connection failed: " . mysqli_connect_error());
 	}

?>