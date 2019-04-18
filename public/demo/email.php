<?php
	require './PHPMailer/class.phpmailer.php';
	$mail             = new PHPMailer();
	/*服务器相关信息*/
	$mail->IsSMTP();   //启用smtp服务发送邮件                     
	$mail->SMTPAuth   = true;  //设置开启认证             
	$mail->Host       = 'smtp.163.com';   	 //指定smtp邮件服务器地址  
	$mail->Username   = 'php_onepzl';  	//指定用户名	
	$mail->Password   = '12171997a';		//邮箱的第三方客户端的授权密码
	/*内容信息*/
	$mail->IsHTML(true);
	$mail->CharSet    ="UTF-8";			
	$mail->From       = 'php_onepzl@163.com';	 		
	$mail->FromName   ="php15";	//发件人昵称
	$mail->Subject    = '邮件发送使用phpmailer'; //发件主题
	$mail->MsgHTML('邮件发送使用phpmailer');	//邮件内容 支持HTML代码

	$mail->AddAddress('2363070555@qq.com');  //收件人邮箱地址
	//$mail->AddAttachment("test.png"); //附件
	$mail->Send();			//发送邮箱
?>