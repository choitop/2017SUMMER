<?php

session_start();
require_once('functions.php');


$mysql_server_name="127.0.0.1";
$mysql_username="root";
$mysql_password=$_SERVER['MYSQL_PSW'];
$mysql_database="cloud";

$path=$_REQUEST['path'];
$_SESSION['file_local_path']=$path;

$user_file=basename($path);
$arr=explode("_", $user_file,2);

$user_name=$arr[0];
$file_name=$arr[1];

$ndecrypt_middleurl="https://cloud/ndecrypt_middle.php";

$houzhui=substr(strrchr($file_name, '.'), 1);
$result=str_replace($houzhui, '', $file_name);

$sign_path=dirname($path)."/sign_".$user_name."_".$result."txt";

$sign=file_get_contents($sign_path);

$link = new PDO("mysql:host=$mysql_server_name;dbname=$mysql_database", "$mysql_username","$mysql_password");
if(!$link)
{
	echo "连接数据库失败";
	exit;
}
$link->query('SET NAMES UTF8');
$str="select * from users where user_name='$user_name'";//在数据库中查找该用户
$result=$link->query($str);
$row = $result->fetch(PDO::FETCH_ASSOC);

//验证签名
$public_key=$row['user_pubkey'];
$flag=signverify($path,$sign,$public_key,'sha256');

if(!$flag)
{
  echo "验证签名失败，停止下载";
  exit;
}
echo "验证签名成功,2秒后跳转到解密文件界面...";
header("refresh:2,$ndecrypt_middleurl");
?>
