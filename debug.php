<html>
<head></head>
<body>
<?php
$output = array();
if(isset($_POST['update']))
{
	echo "Update: ".$_POST['update'];
	if($_POST['update'] == "onoff")
	{
		$res = exec("ssh -o BatchMode=yes -o StrictHostKeyChecking=no -i id_rsa pi@10.8.0.5 irsend SEND_ONCE HEATER ONOFF", $output, $return);
		echo "command sent.";
	}
	else if($_POST['update'] == "onoffPlus")
        {
                $res = exec("ssh -o BatchMode=yes -o StrictHostKeyChecking=no -i id_rsa pi@10.8.0.5 irsend SEND_ONCE HEATER ONOFF ; sleep 0.25 ; irsend SEND_ONCE HEATER UP", $output, $return);
		echo "command sent.";
        }
	else if($_POST['update'] == "restart")
        {
                $res = exec("ssh -o BatchMode=yes -o StrictHostKeyChecking=no -i id_rsa pi@10.8.0.5 sudo systemctl restart controller", $output, $return);
		echo "command sent.";
        }
	else
	{
		echo "unsupported";
	}
}
else
{
?>
	<form method="post">
		Note: Can only switch heater off if on, if heater is off, it will get in an interesting different state :o<br />
		<button name="update" value="onoff">ONOFF Senden</button>
	</form><br />
	<form method="post">
		Note: Can only switch heater on if off, if heater is on, it will get in an interesting different state :o<br />
		<button name="update" value="onoffPlus">ONOFF mit UP Senden</button>
	</form><br />
	<form method="post">
		<button name="update" value="restart">Restart Service</button>
	</form><br />
<?php
}
foreach($output as $line)
{
	echo $line."<br />";
}
?>
</br><a href='/'><button>Back</button></a>
</body>
</html>
