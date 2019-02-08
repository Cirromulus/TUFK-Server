<html>
<head></head>
<body>
<?php
$output = array();
if(isset($_POST['update']))
{
	echo "Update: ".$_POST['update'];
	$res = exec("ssh -v -o BatchMode=yes -o StrictHostKeyChecking=no -i id_rsa pi@10.8.0.5 './takePicture.sh' 2>&1", $output, $return);
}
?>
<img src="/image.jpg?<?php echo filemtime('image.jpg');?>" style="max-width: 100%; max-height:80%; margin:auto; display: block;" alt="Dasn Bild."/>
<form method="post"><button name="update" value="Ja">Neu Aufnehmen</button></form><br />
<?php
foreach($output as $line)
{
	echo $line."<br />";
}
?>
</body>
</html>
