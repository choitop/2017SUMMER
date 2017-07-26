
<html>
<head>
	<meta charset='UTF-8'>
</head>
<?php

$mysql_server_name="127.0.0.1";
$mysql_username="root";
$mysql_password=$_SERVER['MYSQL_PSW'];
$mysql_database="cloud";

$username=$_GET['username'];
$filename=$_GET['filename'];

session_start();
$_SESSION['downfile']=$filename;
$_SESSION['downuser']=$username;

$showfileurl="https://cloud/showfile.php";
$link = new PDO("mysql:host=$mysql_server_name;dbname=$mysql_database", "$mysql_username","$mysql_password");
if(!$link)
{
	header("refresh:2,$showfileurl");
	exit;
}
$link->query('SET NAMES UTF8');
$str="select * from files where user_name='$username' and file_name='$filename'";
$result=$link->query($str);
$row=$result->fetch(PDO::FETCH_ASSOC);
$flag=$row['file_if_shared'];
?>
<body>
<form action="download_file.php" method="post" enctype="multipart/form-data">
	<input type="password" name="pass" placeholder="请输入加密密码">
	<input type="submit" name="submit" value="download">
</form>
</body>
</html>
