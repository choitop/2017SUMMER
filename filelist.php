<html>
<head>
    <meta charset='UTF-8'>
    <style type="text/css">
.div a{ color:#1E90FF}
.div a:visited {color: #696969}
.div a:hover{ color:#4169E1}
</style>
</head>
<body class="div">
<?php
$mysql_server_name="127.0.0.1";
$mysql_username="root";
$mysql_password=$_SERVER['MYSQL_PSW'];
$mysql_database="cloud";
$logurl="https://cloud/login.php";
$cloudurl="https://cloud/cloud.php";
session_start();
$username=$_SESSION['username'];

if(!empty($username)) 
{
  $link = new PDO("mysql:host=$mysql_server_name;dbname=$mysql_database", "$mysql_username","$mysql_password");
  if($link)
  {
    $link->query('SET NAMES UTF8');
    $str="select * from files where user_name='$username' or file_if_shared=1";
    $result=$link->query($str);
    while($row=$result->fetch(PDO::FETCH_ASSOC))
    {
      $arr[]=$row;
    }
    $i=1;
    foreach ($arr as $file)
    {
      printf("<big>%d: </big>",$i);
      $i++;                         
      printf("<a href='download_middle.php?username=%s&filename=%s' >%s_%s</a><br>",$file['user_name'],$file['file_name'],$file['user_name'],$file['file_name']);
    }

  }
  else
  {
    echo "数据库连接失败";
  }
}
else
{
  echo "登录超时，请重新登录！";
}
?>
</body>
</html>