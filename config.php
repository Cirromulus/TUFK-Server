<?php
include_once("settings.php");
echo '<?xml version="1.0" encoding="UTF-8"?>
<Config ';
foreach($confignames as $name)
{
	echo $name[0].'="'.$config[$name[0]].'" ';
}
echo 'serverURI="http://wiewarmistesbei.exsilencio.de/"/>';

$conn->close();
?>
