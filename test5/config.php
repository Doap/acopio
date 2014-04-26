<?php
error_reporting(0);

$host   = "localhost";
$dbname = "ajax_table";
$dbuser = "root";
$dbpass = "admin";

/* PDO connection
$conn = new PDO("mysql:host=$host;dbname=$dbname","$dbuser","$dbpass");
*/

$conn = mysql_connect($host,$dbuser,$dbpass) or die(mysql_error());
		mysql_select_db($dbname,$conn);

?>