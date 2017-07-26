<?php

//对称密钥加密
function encrypt($original_text,$method,$enc_key,$enc_options)
{
  $iv_length = openssl_cipher_iv_length($method);
  $iv = openssl_random_pseudo_bytes($iv_length);
  $ciphertext = openssl_encrypt($original_text, $method, $enc_key, $enc_options, $iv);

  // 定义我们“私有”的密文结构
  $encrypted_file = sprintf('%s$%d$%s$%s', $method, $enc_options, bin2hex($iv), $ciphertext);

  return $encrypted_file;
}


//对称密钥解密
function decrypt($encrypted_text,$key)
{
  // 检查密文格式是否正确、符合我们的定义
  if(preg_match('/.*$.*$.*$.*/', $encrypted_text) !== 1)
  {
    fprintf(STDERR, "无法解密的密文格式\n");
    exit(1);
  }
  // 解析密文结构，提取解密所需各个字段
  list($extracted_method, $extracted_enc_options, $extracted_iv, $extracted_ciphertext) = explode('$', $encrypted_text); 
  $decrypted_text = openssl_decrypt($extracted_ciphertext, $extracted_method, $key, $extracted_enc_options, hex2bin($extracted_iv));
  return $decrypted_text;
}


//签名
function sign($file_path,$private_key,$sign_method)
{
  $file_hash=hash_file($sign_method, $file_path);
  openssl_private_encrypt(hex2bin($file_hash), $sign, $private_key);
  $sign = bin2hex($sign);
  return $sign;
}


//验证签名
function signverify($file_path,$sign,$public_key,$sign_method)
{
  $file_encrypted_hash=hash_file($sign_method, $file_path);
  openssl_public_decrypt(hex2bin($sign), $decrypted_hash, $public_key);
  $decrypted_hash = bin2hex($decrypted_hash);

  if ($file_encrypted_hash==$decrypted_hash) 
  {
    return ture;
  }
  else
  {
    return false;
  }
}

//下载
function downloadFile($file_tmp_path,$contents,$file_name,$file_size)
{
	file_put_contents($file_tmp_path, $contents);
	header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.$file_name.'"');
    header('Expires: 0');
  	header('Cache-Control: must-revalidate');
  	header('Pragma: public');
  	header('Content-Length: '.$file_size);
  	ob_clean();
  	flush();
  	readfile($file_tmp_path);
  	unlink($file_tmp_path);
}

  ?>