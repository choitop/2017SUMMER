<!DOCTYPE html>
<html lang="en" class="app">
<head>  
  <meta charset="utf-8">
  <title>文件下载</title>
  <?php
  session_start();
  ?>

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
                        <iframe src="filelist.php" iframe runat="server" width="1000" height="200" frameborder="no" border="0" marginwidth="0" marginheight="0" scrolling="no" allowtransparency="yes">
                        </iframe>
                      </div>
                      <canvas width="134" height="134"></canvas></div>
                        <div class="h4 m-t m-b-xs"></div>
                          <small class="text-muted m-b"></small>
                        </div>                      
                      </div>
                  </section>
            </section>
          </section>
</body></html>