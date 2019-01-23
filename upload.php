<?php
include_once("settings.php");
if(!isset($_GET["time"]) || !isset($_GET["temp"]) || !isset($_GET["humid"]) || !isset($_GET["actuators"])
	|| floatval($_GET["temp"]) < -20)
{
	$conn->close();
	http_response_code(400);
	die("NAY");
}

$sql = "INSERT INTO ".$temptable." (timestamp, temp, humid, actuatorStatus)
VALUES ('".$conn->real_escape_string($_GET["time"])."', '".$conn->real_escape_string($_GET["temp"])."', '".$conn->real_escape_string($_GET["humid"])."', '".$conn->real_escape_string($_GET["actuators"])."')";

if ($conn->query($sql) === TRUE)
{
	//echo "New record created successfully";
	//$sql = "UPDATE `ips` SET `ip`=\"".$_SERVER['REMOTE_ADDR'] ."\" WHERE 1";
	//$conn->query($sql);
} else
{
	http_response_code(501);
	die("Error: " . $sql . "<br>" . $conn->error);
}
/*
<html>
<head></head>
<body>
<h1>k, whatever</h1>
</body>
</html>
*/
?>
k
<?php
$conn->close();
shell_exec("./plot.py");
?>
