<?php
include_once("settings.php");
$result = $conn->query("SELECT * FROM config LIMIT 1;"); 
$config = mysqli_fetch_assoc($result);

echo '<?xml version="1.0" encoding="UTF-8"?>
<Config targetTemperature="'.$config["targetTemperature"].'" targetHumidity="'.$config["targetHumidity"].'" samplingPeriodSeconds="'.$config["samplingPeriodSeconds"].'" serverConnectionPeriodSeconds="'.$config["serverConnectionPeriodSeconds"].'" serverURI="http://wiewarmistesbei.exsilencio.de/"/>';

$conn->close();
?>
