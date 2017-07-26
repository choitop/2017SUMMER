<?php
session_start();
require_once('functions.php');

$mysql_server_name="127.0.0.1";
$mysql_username="root";
$mysql_password=$_SERVER['MYSQL_PSW'];
$mysql_database="cloud";

$file_local_path=$_SESSION['file_local_path'];

$file_encrypted_contents=file_get_contents($file_local_path);

$pass=$_REQUEST['pass'];

$user_file=basename($file_local_path);

$arr=explode('_', $user_file, 2);

$user_name=$arr[0];
$file_name=$arr[1];


$link = new PDO("mysql:host=$mysql_server_name;dbname=$mysql_database", "$mysql_username","$mysql_password");
if(!$link)
{
  echo "数据库连接失败";
  exit;
}

$link->query('SET NAMES UTF8');
$str="select * from files where file_name='$file_name' and user_name='$user_name'";
$result=$link->query($str);
$row = $result->fetch(PDO::FETCH_ASSOC);

$encrypted_sessionkey=$row['file_encrypted_sessionkey'];
$file_hash=$row['file_hash'];
$file_size=$row['file_size'];

 //解密对称密钥
$decrypted_key=decrypt($encrypted_sessionkey,$pass);
echo "解密密钥完成";
echo "</br>";

//解密文件
$decrypted_file_contents=decrypt($file_encrypted_contents,$decrypted_key);
$decrypted_file_contents=substr($decrypted_file_contents,0,$file_size);


$new_hash=hash('sha256',$decrypted_file_contents);
if($new_hash!=$file_hash)
{
  echo "文件解码错误";
  exit;
}
echo "解码成功";
echo "</br>";



$tmp='downloadtmp/'.$file_name;
downloadFile($tmp,$decrypted_file_contents,$file_name,$file_size);




 ?>
