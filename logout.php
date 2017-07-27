<?php
session_start();
session_unset();
session_destroy();
echo "<script>alert('已登出');history.go(-1);parent.location.href='index.php';</script>";
?>