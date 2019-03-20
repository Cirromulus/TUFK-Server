<?php
include_once("settings.php");
$res = "";
if(isset($_GET['redraw']))
{
	if ( filter_var($_GET['redraw'], FILTER_VALIDATE_INT) === false ) {
 		echo "Your variable is not an integer";
	}
	else
	{
		$res = shell_exec("./plot.py ".$_GET['redraw']);
	}
}
?>
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
$missedTimewindow = time() > $messpunkt['timestamp']+$config["serverConnectionPeriodSeconds"];
if($missedTimewindow)
{
	echo "<strike>";
}
echo "Voraussichtlich nächste Verbindung um ".date("H:i", $messpunkt['timestamp']+$config["serverConnectionPeriodSeconds"])." Uhr.";
if($missedTimewindow)
{
	echo "</strike>";
}
echo "<br />".$res."<br />";
?>
<image style="max-width: 100%;" src="./temp.png?<?php echo filemtime('temp.png'); ?>"/>
<br />
<span style="display: block; text-align: center;"><a href="./cam.php">Kamera</a>, <a href="./setconf.php">Konfiguration ändern</a></span>
<table style="float: left;">
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
<div style="float: right;">
	<form method="post">
		<button name="update" value="onoff">ONOFF Senden</button><br />
		<button name="update" value="onoffPlus">ONOFF mit UP Senden</button><br />
		<button name="update" value="plus">UP Senden</button><br />
		<button name="update" value="restart">Restart Service</button>
	</form>
</div>
</body>
</html>
<?php $conn->close(); ?>
