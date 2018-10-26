<?php
$res = shell_exec("./plot.py");
?>
<html>
<head></head>
<body>
<?php echo $res?>
<br /><img src="/temp.png"/>
</body>
</html>
