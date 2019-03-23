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
<div style="position: absolute;">
<?php
	echo "Zumindest wars das zuletzt am ".date("d.m. H:i", $messpunkt['timestamp']).". ";
	$missedTimewindow = time() > $messpunkt['timestamp']+($config["serverConnectionPeriodSeconds"]+10);
	if($missedTimewindow)
	{
		echo "<strike>";
	}
	echo "Voraussichtlich nächste Verbindung um ".date("H:i", $messpunkt['timestamp']+$config["serverConnectionPeriodSeconds"])." Uhr.";
	if($missedTimewindow)
	{
		echo "</strike>";
	}
	?>
	<span> (<a href="./cam.php">Kamera</a>)</span>
	<br />
	<form style="display: inline;" method="get">
		<input type="text" size="3" name="redraw" value="<?php if(isset($_GET['redraw'])) { echo $_GET['redraw']; } else { echo "7"; }?>"/><button type="submit">days of history</button>
		<?php
		if($res != "") echo $res;
		?>
	</form>
</div>
<image style="max-width: 100%;" src="./temp.png?<?php echo filemtime('temp.png'); ?>"/>
<br />
<div style="display: flex; justify-content: space-evenly; align-items: end;">
	<div style="display: inline-block;">
		<img width="200px" src="/image.jpg?<?php echo filemtime('image.jpg');?>"/><br />
		<form style="display: inline;" method="post" action="/cam.php">
			<button name="update" value="Ja">Neu Aufnehmen</button>
		</form>
	</div>
	<table style="display: inline-block;">
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
	<div style="display: inline-block;">
		<form method="post" action="/setconf.php?update=yes">
		<table>
		<?php
		foreach($confignames as $name)
		{
			echo '<tr><td>'.$name[1].':</td><td><input type="text" name="'.$name[0].'" value="'.$config[$name[0]].'"></td></tr>';
		}
		?>
		</table>
		<input type="submit" value="Submit">
		</form>
	</div>
	<div style="display: inline-block;">
		<form method="post" action="./debug.php">
			<button name="update" value="onoff">ONOFF Senden</button><br />
			<button name="update" value="onoffPlus">ONOFF mit UP Senden</button><br />
			<button name="update" value="plus">UP Senden</button><br />
			<button name="update" value="restart">Restart Service</button>
		</form>
	</div>
</div>
</body>
</html>
<?php $conn->close(); ?>
