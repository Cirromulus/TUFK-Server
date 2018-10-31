<?php include_once("settings.php"); ?>
<html>
<head>
<script type="text/javascript">
function umschalten(referrer)
{
	alert("hah, reingefallen");
}
</script>
</head>
<body>
<h1><?php
$result = $conn->query("SELECT * FROM ".$temptable." ORDER BY timestamp DESC LIMIT 1;"); 
$messpunkt = array();
if($result->num_rows == 0)
{
	echo "Nüscht";
}
else
{
	$messpunkt = mysqli_fetch_assoc($result);
	echo $messpunkt['temp']." Grad mit ".$messpunkt['humid']."% Luftfeuchtigkeit oder so.";
}
?>
</h1>
<?php
echo "Zumindest wars das zuletzt am ".date("d.m. H:i", $messpunkt['timestamp']).". ";
echo "Voraussichtlich nächste Verbindung um ".date("H:i", $messpunkt['timestamp']+$config["serverConnectionPeriodSeconds"])." Uhr.";
echo "</br >";
echo "<span style='color: #FFFFFF'>Last Uploader IP: ".$lastUploaderIP." PS.: You can't see me!</span>"
?>
</br>
<image src="./temp.png"/>
</br>
<table>
<?php
for($el = 0; $el < sizeof($bitpositions); $el++)
{
	echo "<tr><td>".$bitpositions[$el]."</td><td><span style=\"color: ";
	if($messpunkt['actuatorStatus'] & 1 << $el)
	{
		echo "green;\">An";
	}else
	{
		echo "red;\">Aus";
	}
	echo "</span></td><td><button onclick=\"javascript:umschalten(this);\">umschalten</button></td></tr>";
}
?>
</table>
</body>
</html>
<?php $conn->close(); ?>
