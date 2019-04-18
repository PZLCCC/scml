<?php 
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;
/**
 * 后台的公共控制器
 */
class Common extends Controller
{
    public $_user = [];//保存用户的信息
    public $is_check_rule = true;//是否检查权限

    public function __construct()
    {
		// 1、执行父类的构造方法
        parent::__construct();
		// 判断用户是否登陆
        $admin_info = cookie('admin_info');
        if (!$admin_info) {
            $this->error('需要先登陆', 'login/index');
        }
		// 1、读取缓存
        $this->_user = cache('admin_info_' . $admin_info['id']);
        if (!$this->_user) {
			// 说明在缓存中没有得到数据需要查询数据库
			// 将用户的信息存储到属性中
            $this->_user = $admin_info;
			//根据角色的ID获取角色对应的权限信息
            if ($this->_user['role_id'] == 1) {
				// 超级管理员角色下的用户
                $this->is_check_rule = false;//不检查是否有权访问直接使用
				// 获取所有的权限信息因为需要显示导航菜单
                $rules = Db::name('rule')->select();
            } else {
				// 普通角色下的用户
				// 1、获取权限id
                $role_info = Db::name('role')->find($this->_user['role_id']);
				// 2、根据权限的id获取到所有的权限信息
                $rules = Db::name('rule')->where('id', 'in', $role_info['rule_ids'])->select();
            }
			// 将已有的权限信息格式化
            foreach ($rules as $key => $value) {
				// 将导航菜单信息加入到属性中
                if ($value['is_show'] == 1) {
                    $this->_user['menus'][] = $value;
                }
				// 将已有的权限信息把控制器与方法的名称进行拼接保存到属性中
                $this->_user['rules'][] = strtolower($value['controller_name'] . '/' . $value['action_name']);
            }
			// 将运算的结果缓存
            cache('admin_info_' . $admin_info['id'], $this->_user, 3600 * 24);
        }
		// 当从缓存中读取数据导致超级管理员角色会判断权限
        if ($this->_user['role_id'] == 1) {
            $this->is_check_rule = false;
        }
		
		// 权限的检查
        if ($this->is_check_rule) {
			// 普通角色下的用户需要检查权限
			// 组装用户要访问的地址
            $request = Request::instance();
            $url = strtolower($request->controller() . '/' . $request->action());
			// 将后台首页的权限手动追加
            $this->_user['rules'][] = 'index/index';
            $this->_user['rules'][] = 'index/top';
            $this->_user['rules'][] = 'index/menu';
            $this->_user['rules'][] = 'index/main';
            if (!in_array($url, $this->_user['rules'])) {
                if ($request->isAjax()) {
                    return json(['status' => 0, 'msg' => '没有访问的权限']);
                } else {
                    $this->error('没有权限访问');
                }
            }
        }

		// token令牌的检查
        if (config('is_check_token')) {
			// 当为get请求需要生产token令牌 所以不检查
            if (request()->isPost()) {
				// 获取session中的token值
                $session_token = session('__token__');
				// 获取表单所提交的token值
                $token = input('__token__');
				// 1、当session中没有token值说明用户没有get访问表单生产token属于非法操作
				// 2、当表单所提交的内容没有token值 属于非法操作
				// 3、当表单所提交的token值与session中不一致 属于非法操作
                if (!$session_token || !$token || $session_token != $token) {
                    $this->error('令牌错误');
                }
				// 说明token值正确
				// 销毁session中token值 token值只能使用一次
                session('__token__', null);
            }
        }

    }

}

?>