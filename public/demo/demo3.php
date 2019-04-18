<?php

$ch = curl_init();
//开启

$url = 'http://www.scml.com/demo/demo2.php?a=20';

curl_setopt($ch, CURLOPT_URL ,$url);

//设置返回结果不输出
curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);

$res  = curl_exec($ch);

curl_close($ch);