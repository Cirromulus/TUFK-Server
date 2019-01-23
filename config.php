<?php
include_once("settings.php");

echo '<?xml version="1.0" encoding="UTF-8"?>
<Config targetTemperature="'.$config["targetTemperature"].'" temp_lower_limit="'.$config["temp_lower_limit"].'" temp_upper_limit="'.$config["temp_upper_limit"].'" humid_lower_limit="'.$config["humid_lower_limit"].'" humid_upper_limit="'.$config["humid_upper_limit"].'" targetHumidity="'.$config["targetHumidity"].'" samplingPeriodSeconds="'.$config["samplingPeriodSeconds"].'" serverConnectionPeriodSeconds="'.$config["serverConnectionPeriodSeconds"].'" serverURI="http://wiewarmistesbei.exsilencio.de/"/>';

$conn->close();
?>
