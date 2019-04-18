<?php 
namespace app\admin\controller;

use think\Controller;
use app\admin\model\Admin;

/**
 * 测试的控制器
 */
class Test extends Controller
{
    public function uploadtolinux2()
    {
        $image = 'uploads/4.jpg'; //指定本地的要被上传的文件地址
        dump(img_to_cdn($image));
    }
    public function uploadtolinux()
    {
        $image = '1.jpg'; //指定本地的要被上传的文件地址
		// 用于检查本地的地址是否正确;
		// dump(file_exists($image)); 
		// 1、实例化extend目录下的ftp类的对象 在extend目录下类文件可以自动载入
        $ftp = new \ftp('192.168.150.137', '21', 'ftpuser', '123456');
		// dump($ftp);
		// 2、调用方法实现转移
		// 关于第二个参数指定上传的地址 
		// 如果设置为相对地址以/data/res.shop.com目录来相对。因为ftp登陆之后默认就进入此目录下。
		// 如果使用绝对地址即"/"开头的地址 需要对应的目录下拥有操作权限
        dump($ftp->up_file($image, '2.jpg'));

    }
    public function water()
    {
        $dir = './1.jpg';//被处理图片的地址
		// 1、获取image对象
        $img = \think\Image::open($dir);
        $save_name = '2.jpg';//设置最终保存图片的地址
        $img->water('logo.jpg')->save($save_name);
    }
	// 缩略图生成
    public function thumb()
    {
        $dir = './1.jpg';//被处理图片的地址
		// 1、获取image对象
        $img = \think\Image::open($dir);
		//生成缩略图
        $save_name = '2.jpg';//设置最终保存图片的地址
        $img->thumb(400, 400)->save($save_name);
    }
	// 图片裁剪
    public function caijian()
    {
        $dir = './1.jpg';//被处理图片的地址
		// 1、获取image对象
        $img = \think\Image::open($dir);
		// 开始裁剪
        $save_name = '2.jpg';//设置最终保存图片的地址
        $img->crop(200, 200)->save($save_name);
    }
    public function test()
    {
        $data = [
            ['username' => 'a', 'password' => '124'],
            ['username' => 'a', 'password' => '124']
        ];
        $model = model('Admin');
        $obj = $model->isUpdate(false)->saveAll($data);
        dump($obj);
        dump($model);
    }
    public function check6()
    {
		// 代表表单所提交内容
        $data = [
            'name' => 'leo2345',
            'email' => '1qq.com',
            'age' => 18
        ];
        $result = model('Test')->add($data);
        dump($result);
    }
    public function check5()
    {
		// 代表表单所提交内容
        $data = [
            'name' => 'leo2345',
            'email' => '1qq.com',
            'age' => 18
        ];
		// 调用TP控制器基类所提供的验证方法
        $result = $this->validate($data, 'Test.add');
        dump($result);
    }

    public function check4()
    {
		// 代表表单所提交内容
        $data = [
            'name' => 'leo2345',
            'email' => '1qq.com',
            'age' => 18
        ];
		// 获取验证器类的对象 validate 为助手函数获取验证的对象
        $obj = validate('Test');
		// 检查数据  scene方法用于指定使用哪一个场景检查 batch方法用于指定是否批量检查数据合法性。TP默认只有有一个数据不合法直接终止
        $result = $obj->scene('add')->batch(true)->check($data);
        if ($result === false) {
            dump($obj->getError());
            exit();
        }
        echo 'ok';
    }
    public function check3()
    {
		// 代表表单所提交内容
        $data = [
            'name' => 'leo2345',
            'email' => '1qq.com',
            'age' => 20
        ];
		// 获取验证器类的对象 validate 为助手函数获取验证的对象
        $obj = validate('Test');
		// 检查数据
        $result = $obj->check($data);
        if ($result === false) {
            echo $obj->getError();
            exit();
        }
        echo 'ok';
    }
    public function check2()
    {
		// 代表表单所提交内容
        $data = [
            'name' => 'leo',
            'email' => '1@qq.com',
            'age' => 20
        ];
		// 获取验证器类的对象 validate 为助手函数获取验证的对象
        $obj = validate('Test');
		// 检查数据
        $result = $obj->check($data);
        if ($result === false) {
            echo $obj->getError();
            exit();
        }
        echo 'ok';
    }
    public function check()
    {
		// 代表表单所提交内容
        $data = [
            'name' => 'leo',
            'email' => '1@qq.com',
            'age' => 20
        ];
		// 指定验证规则 验证码规则就是对数据格式的要求
		// 由于要针对表单提交的多个数据校验因此需要在指定多个规则因此使用数组格式
		//数组格式中需要对应数据 规则中每一个下标对应数据的下标
        $rule = [
            'name' => 'require|min:6',//多个规则使用每一个规则使用|分割 在每一个规则中:表示指定规则所限制的额外的信息 存在多个每一个是逗号分隔
            'email' => 'email',
            'age' => 'gt:19'
        ];
		// 获取validate类的对象
        $obj = new \think\Validate($rule);
		// 校验数据
        $result = $obj->check($data);
        if ($result === false) {
            echo $obj->getError();
            exit();
        }
        echo 'ok';
    }
}


?>