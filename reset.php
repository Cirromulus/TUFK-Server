<html>
<head></head>
<body>
<?php
$output = array();
if(isset($_POST['update']))
{
	echo "Update: ".$_POST['update'];
	$res = exec("ssh -o BatchMode=yes -o StrictHostKeyChecking=no -i id_rsa pi@10.8.0.5 irsend SEND_ONCE HEATER ONOFF", $output, $return);
}
?>
<form method="post"><button name="update" value="Ja">ONOFF Senden</button></form><br />
Note: Can only switch heater off if on, if heater is off, it will get in an interesting different state :o<br />
<?php
foreach($output as $line)
{
	echo $line."<br />";
}
?>
</body>
</html>
