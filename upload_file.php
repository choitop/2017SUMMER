

<!DOCTYPE html>
<html lang="en" class="app">
<head>
  <meta charset="utf-8">
  <title>普通用户界面</title>

</head>

<body class="bg-black dker2">
  <div class="clearfix text-center m-t"></div>
  <meta name="description" content="app, web app, responsive, admin dashboard, admin, flat, flat ui, ui kit, off screen nav">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <link rel="stylesheet" href="js/jPlayer/jplayer.flat.css" type="text/css">
  <link rel="stylesheet" href="css/bootstrap.css" type="text/css">
  <link rel="stylesheet" href="css/animate.css" type="text/css">
  <link rel="stylesheet" href="css/font-awesome.min.css" type="text/css">
  <link rel="stylesheet" href="css/simple-line-icons.css" type="text/css">
  <link rel="stylesheet" href="css/font.css" type="text/css">
  <link rel="stylesheet" href="css/app.css" type="text/css">
    <!--[if lt IE 9]>
    <script src="js/ie/html5shiv.js"></script>
    <script src="js/ie/respond.min.js"></script>
    <script src="js/ie/excanvas.js"></script>
  <![endif]-->

          <section class="vbox">
            <section class="scrollable wrapper ">
              <div class="col-lg-7"></div>
              <a class="navbar-brand block" href="index.php"><span class="h1 font-bold">CLOUD</span></a>
              <div class="row">
                 <div class="col-lg-7">

                </div>
                <div class="col-lg-5">
                   <section class="panel panel-default rounded">
                      <div class="clearfix text-center m-t">
                        <div class="inline">
                          
                          <div class="h2 m-t m-b-xs font-username">
                            <?php 
                              session_start();
                              echo $_SESSION['username'];
                            ?>
                          </div>
                          <big class="text-muted m-b font-username">普通用户</big>
                        </div>                      
                      </div>

                    <footer class="panel-footer bg-info text-center">
                      <div class="row pull-out">
                        <div class="col-xs-4">
                          <div class="padder-v">
                            <a href="showfile.php" class="m-b-xs h3 block text-white">文件下载</a>
                          </div>
                        </div>
                        <div class="col-xs-4">
                          <div class="padder-v">
                            <a href="upload.php" class="m-b-xs h3 block text-white">文件加密上传</a>
                          </div>
                        </div>
                      </div>
                    </footer>


                  <section class="panel panel-default rounded">
                    <div class="panel-body">
                      <div class="clearfix text-center m-t">
                        <div class="inline">
                          <div class="h4 m-t m-b-xs">
                            <?php
echo "文件上传中";
require_once('functions.php');

$mysql_server_name="127.0.0.1";
$mysql_username="root";
$mysql_password=$_SERVER['MYSQL_PSW'];
$mysql_database="cloud";

$uploadurl="https://cloud/upload.php";
$successurl="https://cloud/upload_success.php";

//判断上传有无错误
if($_FILES["file"]["error"] > 0)
{
  echo "错误：" . $_FILES["file"]["error"] . "<br>";
  header("refresh:2,$uploadurl");
  exit;
}

$file_name=$_FILES["file"]["name"];
$file_tmp_name=$_FILES["file"]["tmp_name"];
$file_type=$_FILES["file"]["type"];
$file_size=$_FILES["file"]["size"];

$file_if_shared=$_REQUEST['check'];
$pass1=$_POST['pass1'];
$pass2=$_POST['pass2'];

if($pass1!=$pass2)
{
  echo "密码不一致，请重新输入";
  header("refresh:2,$uploadurl");
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
  echo "文件类型只能为：office文档、常见图片类型";
  header("refresh:2,$uploadurl");
  exit;
}

if ($file_size>1024*1024*10)
{
  echo "文件大小：< 10MB";
  header("refresh:2,$uploadurl");
  exit;
}

session_start();
$username=$_SESSION['username'];

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
$private_key=file_get_contents("file:////etc/apache2/ssl/apache.key");
$signature=sign($file_tmp_name,$private_key,'sha256');

if ($file_if_shared)
{
  //文件被设为共享文件
  $file_if_shared=1;
}
else
{
  //文件未被设为共享文件，即是私人文件
  $file_if_shared=0;
}

//加密对称密钥
  $method = "aes-256-cbc"; // print_r(openssl_get_cipher_methods());
  $enc_options = 0;
  $encrypted_key = encrypt($enc_key,$method,$pass1,$enc_options);

$new_file_name=$username."_".$file_name;
//将加密后的文件移动到存放上传文件的文件夹upload
move_uploaded_file($file_tmp_name, "upload/".$new_file_name);



//连接数据库
$link = new PDO("mysql:host=$mysql_server_name;dbname=$mysql_database", "$mysql_username","$mysql_password");
if($link)
{
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
    header("refresh:2,$successurl");
    exit;
  }
  else
  {
    //若文件不存在,将 文件名、用户名、加密后的对称密钥、共享文件标志、原文件哈希值、服务器的签名 插入数据库中
    $str="insert into files values('$file_name','$username','$encrypted_key',$file_if_shared,'$file_hash','$signature',$file_size)";
    $link->query($str);
    $link=null;
    header("refresh:2,$successurl");
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
                          </div>
                          <small class="text-muted m-b"></small>
                        </div>
                      </div>
                  </section>
            </section>
          </section>



  <!-- / footer -->
  <script src="js/jquery.min.js"></script>
  <!-- Bootstrap -->
  <script src="js/bootstrap.js"></script>
  <!-- App -->
  <script src="js/app.js"></script>
  <script src="js/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="js/app.plugin.js"></script>
  <script type="text/javascript" src="js/jPlayer/jquery.jplayer.min.js"></script>
  <script type="text/javascript" src="js/jPlayer/add-on/jplayer.playlist.min.js"></script>
  <script type="text/javascript" src="js/jPlayer/demo.js"></script>
<div style="display:none"><script src="http://v7.cnzz.com/stat.php?id=155540&amp;web_id=155540" language="JavaScript" charset="gb2312"></script><script src="http://c.cnzz.com/core.php?web_id=155540&amp;t=z" charset="utf-8" type="text/javascript"></script><a href="http://www.cnzz.com/stat/website.php?web_id=155540" target="_blank" title="站长统计">站长统计</a><script src="http://c.cnzz.com/core.php?web_id=155540&amp;t=z" charset="utf-8" type="text/javascript"></script><a href="http://www.cnzz.com/stat/website.php?web_id=155540" target="_blank" title="站长统计">站长统计</a><a href="http://www.cnzz.com/stat/website.php?web_id=155540" target="_blank" title="站长统计">站长统计</a></div>

</body></html>
