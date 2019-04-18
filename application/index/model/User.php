<?php
namespace app\index\model;
use think\Model;
use think\Db;

class User extends Model
{
    public function regist($data,$type='tel')
    {
        //检查用户名是否相互
        $db_query = Db::name('user');
        if($db_query->where('username',$data['username'])->find()){
            $this->error = '用户名重复';
            return FALSE;
        }
        //数据校验
        if($type='email'){
            //表示为邮箱注册
            //判断邮箱是否重复
            $db_query= Db::name('user');
            if($db_query->where('email',$data['email'])->find()){
                $this->error ='用户名重复';
                return FALSE;
            }
        }else{
            // 检查验证码是否匹配
            $session_data = session('user_regist_code');
		    // 使用session中所记录的验证码与提交的比对
            if (!$session_data || $session_data['code'] != $data['captcha']) {
                $this->error = '验证码错误';
                return false;
            }
		    // 检查验证码是否过期
            if (time() - $session_data['time'] > 600) {
                $this->error = '验证码过期';
                session('user_regist_code', null);//销毁session
                return false;
            }
		    // 检查手机号的重复
            if ($db_query->where('tel', $data['tel'])->find()) {
                $this->error = '手机号重复';
                return false;
            }
            unset($data['captcha']);
            unset($data['makeCaptcha']); 
            $data['status']=1;
        }
        // 运算出密码
		// 运算salt盐的值
        $data['salt'] = rand(100000, 999999);
		// 使用双重md5对用户的密码加密
        $data['password'] = md5(md5($data['password']) . $data['salt']);
        // 用户信息入库
        $db_query->insert($data);
        if($type=='email'){
            //发送激活邮件
            $user_id = $db_query->getLastInsId();
            //生成唯一标识
            $key = uniqid();
            \redisCache::instance()->set($key,$user_id,3600);
            //组装发送邮件的的链接地址
            $message = url('active','key='.$key,true,true);
            //发送邮件
            send_email($data['email'],$message);
        }else{
            session('user_regist_code', null);//销毁session
        }
       
        
    }
    public function login($name,$password)
    {
        $db_query = Db::name('user');
        $user_info = $db_query->where('username',$name)->find();
        if(!$user_info){
            $this->error = '用户名错误';
            return FALSE;
        }
        if($user_info['password'] != md5(md5($password).$user_info['salt'])){
            $this->error='密码错误';
            return FALSE;
        }
        if(!$user_info['status'] == 1){
            $this->error='未激活不能登陆';
            return FALSE;
        }
        session('user_info',$user_info);

        model('Cart')->cookie2db();
    }
}