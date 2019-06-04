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

$result = $conn->query("SELECT * FROM config ORDER BY id DESC LIMIT 1;");
if(!isset($result->field_count))
{
        // sql to create table
	die("Config table not found.");
        $sql = "
	CREATE TABLE IF NOT EXISTS `config` (
	  `id` int(11) NOT NULL,
	  `targetTemperature` float NOT NULL DEFAULT 16,
	  `temp_max_delta` int(3) NOT NULL,
	  `temp_upper_limit` float NOT NULL DEFAULT 1,
	  `temp_lower_limit` float NOT NULL DEFAULT 1,
	  `targetHumidity` float NOT NULL DEFAULT 65,
	  `humid_lower_limit` float NOT NULL DEFAULT 2.5,
	  `humid_upper_limit` float NOT NULL DEFAULT 2.5,
	  `samplingPeriodSeconds` int(11) NOT NULL DEFAULT 10,
	  `serverConnectionPeriodSeconds` int(11) NOT NULL DEFAULT 1750,
	  `actuatorOverride` int(4) NOT NULL DEFAULT 0,
	  PRIMARY KEY (`id`),
	  UNIQUE KEY `id` (`id`),
	  KEY `id_2` (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;

	INSERT INTO `config` (`id`, `targetTemperature`, `temp_max_delta`, `temp_upper_limit`, `temp_lower_limit`, `targetHumidity`, `humid_lower_limit`, `humid_upper_limit`, `samplingPeriodSeconds`, `serverConnectionPeriodSeconds`, `actuatorOverride`) VALUES
	(0, 13, 8, 2, 0, 70, 5, 0, 15, 260, 0);
	";

        if ($conn->query($sql) === TRUE) {
            echo "Table config created successfully";
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
$confignames = array(
        array("targetTemperature", "Temperature	Minimum"),
        array("temp_max_delta", "Maximum Temperature Deviation"),
        array("temp_upper_limit", "Upper Temperature Threshold Delta"),
        array("temp_lower_limit", "Lower Temperature Threshold Delta"),
        array("targetHumidity",	"Humidity Maximum"),
        array("humid_lower_limit", "Lower Humidity Threshold Delta"),
        array("humid_upper_limit", "Upper Humidiy Threshold Delta"),
        array("samplingPeriodSeconds", "Sampling Period	in Seconds"),
        array("serverConnectionPeriodSeconds", "Server Connection Period in Seconds"),
        array("actuatorOverride", "Actuator Overrides"),
);
?>
