<?php

session_start();
require_once('functions.php');

$mysql_server_name="127.0.0.1";
$mysql_username="root";
$mysql_password=$_SERVER['MYSQL_PSW'];
$mysql_database="cloud";

$file_path="upload/";

$downloadurl="Location:https://cloud/filelist.php";
session_start();
$file_name=$_SESSION['downfile'];
$user_name=$_SESSION['downuser'];

$pass=$_REQUEST['pass'];


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

  $link->query('SET NAMES UTF8');
  $str="select * from users where user_name='$user_name'";//在数据库中查找该用户
  $result=$link->query($str);
  $row = $result->fetch(PDO::FETCH_ASSOC);

  $public_key=$row['user_pubkey'];

  $file_path=$file_path.$user_name."_".$file_name;
  $file_contents=file_get_contents($file_path);

  //验证签名
  $flag=signverify($file_path,$sign,$public_key,'sha256');
  if(!$flag)
  {
    echo "验证签名失败，停止下载";
    exit;
  }
  echo "验证签名成功";
  echo "</br>";


  //解密对称密钥
  $decrypted_key=decrypt($encrypted_sessionkey,$pass);



  //解密文件
  $decrypted_file_contents=decrypt($file_contents,$decrypted_key);
  $decrypted_file_contents=substr($decrypted_file_contents,0,$file_size);

  //验证哈希值，检查文件完整性
  $new_hash=hash('sha256',$decrypted_file_contents);
  if($new_hash!=$file_hash)
  {
    echo "文件解码错误";
    exit;
  }
  echo "文件解码成功！正在生成下载页面";
  echo "</br>";

  //下载文件
  $tmp='downloadtmp/'.$file_name;
  downloadFile($tmp,$decrypted_file_contents,$file_name,$file_size);

}
else
{
  echo "数据库连接失败";
}


?>
