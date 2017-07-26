<?php
require_once('functions.php');

$user_name=$_GET['username'];
$file_name=$_GET['filename'];

$file_path="upload/".$user_name."_".$file_name;

$file_contents=file_get_contents($file_path);


$tmp="downloadtmp/".$user_name."_".$file_name;
$new_file_name=$user_name.'_'.$file_name;
downloadFile($tmp,$file_contents,$new_file_name,filesize($file_path));

 ?>
