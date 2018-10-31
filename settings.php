<?php

/*
$inipath = php_ini_loaded_file();
if ($inipath) {
    echo 'Loaded php.ini: ' . $inipath;
} else {
    echo 'A php.ini file is not loaded';
}
*/

date_default_timezone_set("Europe/Berlin");

$servername = "localhost";
$username = "warmkram";
$password = "warmkram";
$temptable = "templog";

$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "USE ".$username.";";

if ($conn->query($sql) === TRUE) {
    //echo "Database connect successfully";
} else {
    die("Error connecting to database: " . $conn->error);
}

$sql = "SELECT * FROM ".$temptable." LIMIT 1;";
$result = $conn->query($sql);
if(!isset($result->field_count))
{
	// sql to create table
	$sql = "CREATE TABLE templog (
	timestamp BIGINT UNSIGNED PRIMARY KEY UNIQUE INDEX,
	temp FLOAT,
	humid FLOAT,
	actuatorStatus INT(2) UNSIGNED
	)";

	if ($conn->query($sql) === TRUE) {
	    echo "Table created successfully";
	} else {
	    echo "Error creating table: " . $conn->error;
	}
}else
{
	//echo "table exists";
}

$lastUploaderIP = 0;
$sql = "SELECT * FROM ips LIMIT 1;";
$result = $conn->query($sql);
if(!isset($result->field_count))
{
        // sql to create table
        $sql = "CREATE TABLE ips (
        ip CHAR(16)
        )";

	if ($conn->query($sql) === TRUE) {
            echo "Table ips created successfully";
        } else {
            echo "Error creating table: " . $conn->error;
        }
}else
{
        //echo "table exists";
	$lastUploaderIP = mysqli_fetch_assoc($result)['ip'];
}

$result = $conn->query("SELECT * FROM config LIMIT 1;");
if(!isset($result->field_count))
{
        // sql to create table
	die("Config table not found.");
        $sql = "CREATE TABLE config (
        ip CHAR(16)
        )";

        if ($conn->query($sql) === TRUE) {
            echo "Table ips created successfully";
        } else {
            echo "Error creating table: " . $conn->error;
        }
}

$config = mysqli_fetch_assoc($result);

$bitpositions = array
(
	"Ventilator",
	"Heizlüfter",
	"Bewegungssensor",
	"Feueralarm<br><small>(oha!)</small>"
);
?>
