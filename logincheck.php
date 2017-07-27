<?php
header("Content-type:text/html;charset=utf-8");

$mysql_server_name="127.0.0.1";
$mysql_username="root";
$mysql_password=$_SERVER['MYSQL_PSW'];
$mysql_database="cloud";

//定义跳转链接
$regurl="https://cloud/register.php";
$logurl="https://cloud/login.php";
$cloudurl="https://cloud/cloud.php";

//连接数据库
$link=new PDO("mysql:host=$mysql_server_name;dbname=$mysql_database", "$mysql_username", "$mysql_password");

if($link)
{
    if(isset($_POST["logsub"]))
    {
      $name=$_POST["username"];
      $password=$_POST["password"];

      $link->query('SET NAMES UTF8');

      if($name==""||$password=="")//判断是否输入完整
      {
        echo "<script>alert('请填写完整！');history.go(-1);parent.location.href='login.php';</script>";
        $link=null;//断开数据库连接
        exit;
      }

      $str="select * from users where user_name='$name'";//在数据库中查找该用户
      $result=$link->query($str);
      $row = $result->fetch(PDO::FETCH_ASSOC);

      if($row==null)//密码为空，则用户不存在
      {
        echo "<script>alert('此用户不存在，请先注册！');history.go(-1);parent.location.href='login.php';</script>";
        $link=null;
        exit;
      }

      $user_hash=$row['user_pswd_hash'];
      $user_salt=$row['user_salt'];
      $iterations=1000;
      $user_login_hash = hash_pbkdf2("sha256", $password, $user_salt, $iterations, 20);

      if($user_hash==$user_login_hash)//判断密码与注册时密码是否一致
      {
        echo "<script>alert('登录成功！');history.go(-1);parent.location.href='cloud.php';</script>";
        session_start();
        $_SESSION['username']=$name;
        $_SESSION['password']=$password;
        $link=null;//断开数据库连接
        exit;
      }
      else
      {
        echo "<script>alert('密码错误，登录失败！请重新输入！');history.go(-1);parent.location.href='login.php';</script>";
        $link=null;//断开数据库连接
        exit;
      }
    }
}
else {
  echo "数据库连接失败";
  exit;
}
?>
