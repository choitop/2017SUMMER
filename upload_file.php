<?php
require_once('functions.php');

$mysql_server_name="127.0.0.1";
$mysql_username="root";
$mysql_password=$_SERVER['MYSQL_PSW'];
$mysql_database="cloud";

$uploadurl="https://cloud/upload.php";

//判断上传有无错误
if($_FILES["file"]["error"] > 0)
{
  echo "错误：" . $_FILES["file"]["error"] . "<br>";
  header("refresh:2,$uploadurl");
  exit;
}
session_start();
$username=$_SESSION['username'];

$file_name=$_FILES["file"]["name"];
$file_tmp_name=$_FILES["file"]["tmp_name"];
$file_type=$_FILES["file"]["type"];
$file_size=$_FILES["file"]["size"];

$file_if_shared=$_REQUEST['check'];
$pass=$_POST['pass'];
$token=$_POST['token'];

$link=new PDO("mysql:host=$mysql_server_name;dbname=$mysql_database", "$mysql_username", "$mysql_password");
if($link)
{ 
  $str="select * from users where user_name='$username'";//在数据库中查找该用户
  $result=$link->query($str);
  $row = $result->fetch(PDO::FETCH_ASSOC);

  $encrypted_privatekey=$row['user_prikey'];
  $user_hash=$row['user_pswd_hash'];
  $user_salt=$row['user_salt'];
  $iterations=1000;
  $input_pass_hash = hash_pbkdf2("sha256", $pass, $user_salt, $iterations, 20);

  if($input_pass_hash!=$user_hash)
  {
    echo "<script>alert('登录密码不一致，请重新输入');history.go(-1);parent.location.href='upload.php';</script>";
    $link=null;//断开数据库连接
    exit;
  }


//检查文件类型和文件大小是否合法
  $typeFlag=false;
  switch ($file_type)
  {

    case 'image/pjpeg':
      $typeFlag=true;
      break;
    case 'image/jpeg':
      $typeFlag=true;
      break;
    case 'image/gif':
      $typeFlag=true;
      break;
    case 'image/png':
      $typeFlag=true;
      break;
    case 'image/x-png':
      $typeFlag=true;
      break;
    case 'application/pdf':
      $typeFlag=true;
      break;
    case 'application/msword':
      $typeFlag=true;
      break;
    case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
      $typeFlag=true;
      break;
    case 'text/plain':
      $typeFlag=true;
      break;
    case 'application/vnd.ms-powerpoint':
      $typeFlag=true;
      break;
    case 'application/vnd.ms-excel':
      $typeFlag=true;
      break;
    case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
      $typeFlag=true;
      break;
    case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
      $typeFlag=true;
      break;
    default:
      break;
  }

  if ($typeFlag==false)
  {
    echo "<script>alert('文件类型只能为：office文档、常见图片类型');history.go(-1);parent.location.href='upload.php';</script>";
    exit;
  }

  if ($file_size>1024*1024*10)
  {
    echo "<script>alert('文件大小：< 10MB');history.go(-1);parent.location.href='upload.php';</script>";
    exit;
  }


  $file_contents=file_get_contents($file_tmp_name);

  //对原文件哈希
  $file_hash=hash_file('sha256', $file_tmp_name);

  //padding
  $block_size = 256;
  $padding_char = $block_size - (strlen($file_contents) % $block_size);
  $file_contents .= str_repeat(chr($padding_char), $padding_char);


  //加密文件
  $method = "aes-256-cbc"; // print_r(openssl_get_cipher_methods());
  $enc_key = bin2hex(openssl_random_pseudo_bytes(32)); // 对称加密秘钥，应妥善保存
  $enc_options = 0;
  $encrypted_file = encrypt($file_contents,$method,$enc_key,$enc_options);

  file_put_contents($file_tmp_name, $encrypted_file);


  //对加密后的文件进行哈希并签名
  $private_key=decrypt($encrypted_privatekey,$pass);
  $signature=sign($file_tmp_name,$private_key,'sha256');

  if ($file_if_shared)
  {
    //文件被设为共享文件
    $file_if_shared=1;
    $pass1=$token;
  }
  else
  {
    //文件未被设为共享文件，即是私人文件
    $file_if_shared=0;
    $pass1=$pass;
  }

  //加密对称密钥
  $method = "aes-256-cbc"; // print_r(openssl_get_cipher_methods());
  $enc_options = 0;
  $encrypted_key = encrypt($enc_key,$method,$pass1,$enc_options);

  $new_file_name=$username."_".$file_name;
  //将加密后的文件移动到存放上传文件的文件夹upload
  move_uploaded_file($file_tmp_name, "upload/".$new_file_name);



 
  $link->query('SET NAMES UTF8');
    
  $str="select count(*) from files where file_name='$file_name' and user_name='$username'";
  $result=$link->query($str);
  $row=$result->fetchColumn();
  $count=(int)$row;


  if($count!=0)
  {
    //若文件存在，则将原来记录更新
    $str="update files set file_encrypted_sessionkey='$encrypted_key',file_if_shared=$file_if_shared,file_hash='$file_hash',file_sign='$signature',file_size=$file_size where file_name='$file_name' and user_name='$username'";
    $link->query($str);
    $link=null;
    echo "<script>alert('上传成功！');history.go(-1);parent.location.href='showfile.php';</script>";
    exit;
  }
  else
  {
    //若文件不存在,将 文件名、用户名、加密后的对称密钥、共享文件标志、原文件哈希值、服务器的签名 插入数据库中
    $str="insert into files values('$file_name','$username','$encrypted_key',$file_if_shared,'$file_hash','$signature',$file_size)";
    $link->query($str);
    $link=null;
    echo "<script>alert('上传成功！');history.go(-1);parent.location.href='showfile.php';</script>";
    exit;
  }
}
  
else
{
  echo "数据库连接失败";
  header("refresh:2,$uploadurl");
  exit;
}


?>
