<?php
include_once("settings.php");

if(isset($_GET['update']))
{
	$statement = "UPDATE `config` SET ";
	foreach($confignames as $name)
	{
		if(isset($_POST[$name[0]]))
		{
			$statement .= '`'.$name[0].'`='.$_POST[$name[0]].', ';
		}
	}
	$statement = substr($statement, 0, -2);
	$statement .= " WHERE 1;";
	if(($result = $conn->query($statement)) === TRUE)
	{
		echo "update OK";
		echo "</br><a href='/'><button>Back</button></a>";
		die();
	}
	else
	{
		echo $statement;
		die( "update failed!</br>".$result );
	}
}
?>
<html>
<head></head>
<body>
<form method="post" action="?mega_secure=yes&update">
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

</body>
</html>

<?php
$conn->close();
?>
