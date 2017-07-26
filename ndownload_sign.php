<?php
require_once('functions.php');
session_start();
ob_clean();

$mysql_server_name="127.0.0.1";
$mysql_username="root";
$mysql_password=$_SERVER['MYSQL_PSW'];
$mysql_database="cloud";

$file_path="upload/";

$user_name=$_GET['username'];
$file_name=$_GET['filename'];

//var_dump($user_name);
//var_dump($file_name);

$link = new PDO("mysql:host=$mysql_server_name;dbname=$mysql_database", "$mysql_username","$mysql_password");
if($link)
{

  $link->query('SET NAMES UTF8');
  $str="select * from files where file_name='$file_name' and user_name='$user_name'";
  $result=$link->query($str);
  $row = $result->fetch(PDO::FETCH_ASSOC);

  //取出每列数据
  $encrypted_sessionkey=$row['file_encrypted_sessionkey'];
  $file_if_shared=$row['file_if_shared'];
  $file_hash=$row['file_hash'];
  $sign=$row['file_sign'];
  $file_size=$row['file_size'];

  $houzhui=substr(strrchr($file_name, '.'), 1);
  $result=str_replace($houzhui, '', $file_name);

//下载签名
  $tmp='downloadtmp/sign_'.$user_name.'_'.$result.'txt';
  $sign_file_name='sign_'.$user_name.'_'.$result.'txt';
  downloadFile($tmp,$sign,$sign_file_name,strlen($sign));

}
else
{
  echo "数据库连接失败";
}


?>
