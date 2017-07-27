<!DOCTYPE html>
<html lang="en" class="app">
<head>  
<meta charset="utf-8">
<title>文件加密上传</title>
<?php
  session_start();
  ?>
  <style>
.input_style_disable{
  visibility: hidden;
}
.input_style_able{
  visibility: visible;
}
</style>

   <script type="text/javascript" src="https://img3.doubanio.com/f/accounts/235752090cae6e105c6bb570481adfb4fcdc7b4e/js/lib/cookie.js"></script>
  <script type="text/javascript" src="https://img3.doubanio.com/f/accounts/c5268df4c1f0bada95cb3d2b80089a50b494b5ee/js/lib/jquery.min.js"></script>
 
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
              <a class="navbar-brand block" href="logout.php"><span class="h1 font-bold">LOGOUT</span></a>           
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
                           session_start();
                          if ($_SESSION['username']==NULL) {
                            echo "登录超时，请重新登录！";
                          }
                          else{
                          ?>
                          	<form action="upload_file.php" method="post" enctype="multipart/form-data">
							              <label for="file">文件名</label>
              							<input type="file" name="file" id="file" /> 
                                        <label><input type="checkbox" name="check">设为共享文件</label>
                                        <div><input type="token" name="token" placeholder="请输入共享密码" class="input_style_disable" /></div>
                                        <div><input type="password" name="pass" placeholder="请输入登录密码"/></div>   
              							<br />
              							<input type="submit" name="submit" class="btn btn-info rounded" value="上传">
                            <input type="reset" name="reset" class="btn btn-info rounded" value="重置" /> 
              							</form>
                            <?php
                          }
                            ?>
                          </div>
                          <small class="text-muted m-b"></small>
                        </div>                      
                      </div>
                  </section>
                </div>
              </div>
            </section>
          </section>
<script>
$("input[name='check']").click(function(){
        if ($("input[name='check']").is(':checked')) {
            $("input[name='token']").attr('input_style_disable', false).addClass('input_style_able');
        } else {
            $("input[name='token']").attr('input_style_disable', true).removeClass('input_style_able');
        }

    });
</script>
</body></html>
