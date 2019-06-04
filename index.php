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
function postOverride(id, value)
{
	//alert("old overrides: " + <?php echo $config["actuatorOverride"];?>);
	//alert(id + " " + value);
        override = <?php echo $config["actuatorOverride"];?>;
        override = override & ~(0b11 << id * 2) | (value << id * 2);
	//alert("new overrides: " + override.toString(2));
        
	var xhr = new XMLHttpRequest();
	xhr.open("POST", "/setconf.php?update=yes", true);
	xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	xhr.onreadystatechange = function() {//Call a function when the state changes.
    	   if(xhr.readyState == 4 && xhr.status == 200) {
       	      location.reload();
    	   }
	}
	xhr.send("actuatorOverride="+override);
}
</script>
<style>
.infobar {
    display: flex;
    justify-content: space-evenly;
    align-items: inherit;
}

.infobar .child {
    display: inline-block;
}

#switcher button {
    padding: 0;
    margin: 0 1px;
}

#switcher button:disabled {
    font-weight: bold;
}

</style>
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
<div class="infobar">
	<div class="child">
		<img width="200px" src="/image.jpg?<?php echo filemtime('image.jpg');?>"/><br />
		<form style="display: inline;" method="post" action="/cam.php">
			<button name="update" value="Ja">Neu Aufnehmen</button>
		</form>
	</div>
	<table class="child" id="switcher">
		<?php
		for($el = 0; $el < sizeof($bitpositions); $el++)
		{
			$override = ($config["actuatorOverride"] & 0b11 << ($el * 2)) >> ($el * 2);
			echo "<tr><td>".$bitpositions[$el]."</td><td><span style=\"color: ";
			if($messpunkt['actuatorStatus'] & 1 << $el){
				echo "green;\">An";
			}else{
				echo "red;\">Aus";
			}
			echo "</span></td><td><button ";
			if($override == 0b11){
				echo "disabled";
			}else{
				echo "onclick='postOverride(".$el.",0b11)'";
			}
			echo ">An</button><button ";
			if($override == 0b00){
				echo "disabled";
			}else{
				echo "onclick='postOverride(".$el.",0b00)'";
			}
			echo ">Auto</button><button ";
			if($override == 0b10){
				echo "disabled";
			}else{
				echo "onclick='postOverride(".$el.",0b10)'";
			}
			echo ">Aus</button></td></tr>";
		}
		?>
	</table>
	<div class="child">
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
	<div class="child">
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
