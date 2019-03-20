<html>
<head></head>
<body>
<?php
if(isset($_POST['update']))
{
    $output = array();
	echo "Update: ".$_POST['update']." ";
	$output = array();
	if($_POST['update'] == "onoff")
	{
		$res = exec("ssh -o BatchMode=yes -o StrictHostKeyChecking=no -i id_rsa pi@10.8.0.5 irsend SEND_ONCE HEATER ONOFF", $output, $return);
		echo "command sent.";
	}
	else if($_POST['update'] == "onoffPlus")
	{
        	$res = exec("ssh -o BatchMode=yes -o StrictHostKeyChecking=no -i id_rsa pi@10.8.0.5 'irsend SEND_ONCE HEATER ONOFF ; sleep 1 ; irsend SEND_ONCE HEATER UP'", $output, $return);
		echo "command sent.";
	}
	else if($_POST['update'] == "plus")
	{
    	$res = exec("ssh -o BatchMode=yes -o StrictHostKeyChecking=no -i id_rsa pi@10.8.0.5 irsend SEND_ONCE HEATER UP", $output, $return);
		echo "command sent.";
	}
	else if($_POST['update'] == "restart")
	{
		$res = exec("ssh -o BatchMode=yes -o StrictHostKeyChecking=no -i id_rsa pi@10.8.0.5 sudo systemctl restart controller 2>&1", $output, $return);
		echo "command sent.";
	}
	else
	{
		echo "unsupported";
	}
	foreach($output as $line)
	{
		echo $line."<br />";
	}
}
else
{
?>
	<form method="post">
		Note: Can only switch heater off if on, if heater is off, it will get in an interesting different state :o<br />
		<button name="update" value="onoff">ONOFF Senden</button><br />
		Note: Can only switch heater on if off, if heater is on, it will get in an interesting different state :o<br />
		<button name="update" value="onoffPlus">ONOFF mit UP Senden</button><br />
		Note: Can only switch heater on if half mode<br />
		<button name="update" value="plus">UP Senden</button><br />
		<button name="update" value="restart">Restart Service</button>
	</form><br />
<?php
}
?>
<br /><a href='/'><button>Back</button></a>
</body>
</html>
