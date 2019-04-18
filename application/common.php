<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

use think\Config;
//邮箱
if(!function_exists('send_email')){
    function send_email($to,$message,$Subject='邮箱注册激活'){
        require '../extend/PHPMailer/class.phpmailer.php';
        $mail = new \PHPMailer();
	/*服务器相关信息*/
        $mail->IsSMTP();   //启用smtp服务发送邮件                     
        $mail->SMTPAuth = true;  //设置开启认证             
        $mail->Host = 'smtp.163.com';   	 //指定smtp邮件服务器地址  
        $mail->Username = 'php_onepzl';  	//指定用户名	
        $mail->Password = '12171997a';		//邮箱的第三方客户端的授权密码
	/*内容信息*/
        $mail->IsHTML(true);
        $mail->CharSet = "UTF-8";
        $mail->From = 'php_onepzl@163.com';
        $mail->FromName = "php15";	//发件人昵称
        $mail->Subject = $Subject; //发件主题
        $mail->MsgHTML($message);	//邮件内容 支持HTML代码

        $mail->AddAddress($to);  //收件人邮箱地址
	//$mail->AddAttachment("test.png"); //附件
        $mail->Send();			//发送邮箱
    }
}
if(!function_exists('send_sms')){
    function send_sms($to,$datas,$tempId=1){
            //主帐号,对应开官网发者主账号下的 ACCOUNT SID
        $accountSid = '8a216da8697b8029016990e9768008e2';
    //主帐号令牌,对应官网开发者主账号下的 AUTH TOKEN
        $accountToken = 'c42d82c919cf41ccb203b979f439f1ff';
    //应用Id，在官网应用列表中点击应用，对应应用详情中的APP ID
    //在开发调试的时候，可以使用官网自动为您分配的测试Demo的APP ID
        $appId = '8aaf0708697b6beb016990ef899d08a3';
    //请求地址
    //沙盒环境（用于应用开发调试）：sandboxapp.cloopen.com
    //生产环境（用户应用上线使用）：app.cloopen.com
        $serverIP = 'app.cloopen.com';
    //请求端口，生产环境和沙盒环境一致
        $serverPort = '8883';
    //REST版本号，在官网文档REST介绍中获得。
        $softVersion = '2013-12-26';

        $rest = new \sms($serverIP,$serverPort,$softVersion);
        $rest ->setAccount($accountSid,$accountToken);
        $rest->setAppId($appId);

        //调用方法发生验证码
        $result = $rest->sendTemplateSMS($to,$datas,$tempId);
        if($result == NULL){
            return FALSE;
        }
        if($result->statusCode!=0){
            return FALSE;
        }
        return TRUE;
    }
}
if(!function_exists('get_user_id'))
    {
        function get_user_id(){
            //获取用户信息
            $user_info = session('user_info');
            if(!$user_info){
                return FALSE;
            }
            return $user_info['id'];
        }
    }
if(!function_exists('img_to_cdn'))
    {
        function img_to_cdn($local_path,$server_path=''){
            $server_path = $server_path?$server_path:$local_path;
            //读取ftp服务器的配置信息
            $server = Config::get('ftp_server');
            $ftp = new \ftp($server['host'],$server['port'],$server['user'],$server['pass']);
            return $ftp->up_file($local_path,$server_path);
        }
    }
if (!function_exists('get_tree')) {
        /*作用：对数据进行格式化
        $data：被格式化的数据
        $id：要查找分类的id 为0表示查找所有分类的信息
        $lev:标注分类的层次
        */
    function get_tree($data,$id=0,$lev=0,$is_clear=false){
        static $list = [];
        if($is_clear){
            $list=[];
        }
        foreach ($data as $value)
        {
            if ($value['parent_id'] == $id) {
                $value['lev']=$lev;
                $list[]=$value;
                get_tree($data,$value['id'],$lev+1);
            }
        }
        return $list;
    }

}