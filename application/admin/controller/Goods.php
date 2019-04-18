<?php 
namespace app\admin\controller;

use think\Request;
use think\Db;
/**
 * 
 */
class Goods extends Common
{
    
    //根据类型id使用ajax获取属性信息
    public function showAttr()
    {
        $type_id = input('type_id');
        $data = model('Attribute')-> getAttrByTypeID($type_id);
        $this-> assign('data',$data);
        return $this->fetch();
    }
    //切换商品的推荐状态
    public function changeStatus()
    {
        $goods_id = input('goods_id/d');
        $field = input('field');

        $status = model('Goods')->changeStatus($goods_id,$field);

        return json(['status'=>$status,'code'=>1]);
    }
    //商品彻底删除
    public function delete()
    {
        $goods_id = input('id');

        $model = model('Goods');
        $result = $model -> deleteGoods($goods_id);
        if($result === FALSE){
            $this -> error($model->getError());
        }
        $this->success('ok');
    }
    //商品的还原
    public function reduction()
    {
        $goods_id = input('id');

        Db::name('Goods')->where('id',$goods_id)->setField('is_del',0);
        $this -> success('ok','index');
    }
    //商品回收站
    public function recycle()
    {
        $model = model('Goods');

        $category = model('Category')->getTree();

        $this -> assign('category',$category);
        $data = $model->listData(1);
        $this -> assign('data',$data);
        return $this->fetch();
    }
    //商品伪删除
    public function remove()
    {
        $goods_id = input('id');

        Db::name('Goods')->where('id',$goods_id)->setField('is_del',1);
        $this -> success('ok','index');
    }
    //商品的编辑
    public function edit(Request $request)
    {
        $goods_model = model('Goods');
        $goods_id =input('id');
        if($request->isGet()){
            $category = model('Category')->getTree();
            $this->assign('category',$category);

            //获取当前商品的信息
            $info = $goods_model -> getGoodsInfo($goods_id);
            $this->assign('info',$info);
            return $this->fetch();
        }
        $data = input();
        $this -> uploadGoodsThumb($data,false);

        $this -> checkGoodsSn($data,true);
        $result = $this -> validate($data,'Goods');

        if($result !== TRUE){
            $this -> error($result);
        }
        $result = $goods_model->editGoods($data);

        if($result === FALSE){
            $this -> error($goods_model->getError());
        }
        $this -> success('ok','index');
    }
    //商品列表的显示
    public function index()
    {
        $model = model('Goods');
        $category = model('Category')->getTree();

        $this->assign('category',$category);

        $data = $model->listData();

        $this-> assign('data',$data);

        return $this->fetch();
    }
	// 商品添加
    public function add(Request $request)
    {
        if ($request->isGet()) {
            $type = model('Type')->listData();
            $this->assign('type',$type);
            $category = model('Category')->getTree();
            $this->assign('category', $category);
            return $this->fetch();
        }
        $data = input();
		// 实现商品图片的上传
        $this->uploadGoodsThumb($data);
		// 检查货号是否正确
        $this->checkGoodsSn($data);
		// 验证数据合法性
        $result = $this->validate($data, 'Goods');
        if ($result !== true) {
            $this->error($result);
        }
        $model = model('Goods');
        $result = $model->addGoods($data);
        if ($result === false) {
            $this->error($model->getError());
        }
        $this->success('ok', 'index');
    }
	// 商品的图片上传
    protected function uploadGoodsThumb(&$data,$is_must=true)
    {
		// 1、获取file类的对象
        $file = request()->file('goods_img');
        if (!$file) {
            if($is_must){
                // 没有上传文件
                $this->error('文件必须上传');
            }else{
                return TRUE;
        }
    }
		// 2、使用file对象调用move方法实现文件的上传
        $upload_base = config('upload_base');//从配置项读取出上传的根目录
        $info = $file->validate(['ext' => 'jpg,png'])->move($upload_base);
        if (!$info) {
			// 文件上传有错误
            $this->error($file->getError());
        }
		// 3、提取上传之后的文件地址
        $goods_img = $upload_base . '/' . $info-> getSaveName();
		// 4、更换地址中的\为/
		// 在PHP代码使用图片时当使用相对地址格式顶头不要有/ 当在浏览器使用文件地址时 /使用表示为域名 如果省略 会安装当前的地址请求
        $data['goods_img'] = str_replace('\\', '/', $goods_img);
		
		// 根据上次的图片生成缩略图
		// 打开图片获取对象
        $img = \think\Image::open($data['goods_img']);
		// 计算生成缩略图的保存地址
		// 保存的地址与原图存储在同一个目录文件名称在原图的基础上增加thumb_前缀
        $data['goods_thumb'] = $upload_base . '/' . date('Ymd') . '/thumb_' . $info->getFileName();
		// 生成缩略图
        $img->thumb(200, 200)->save($data['goods_thumb']);
		// 将本地的图片转移到资源服务器下
        img_to_cdn($data['goods_img']);//转移原图
        img_to_cdn($data['goods_thumb']);//转移缩略图
    }

	// 检查货号是否正确
    protected function checkGoodsSn(&$data,$is_check_me=false)
    {
		// 如果有传递货号检查唯一否则系统生成一个货号
        if ($data['goods_sn']) {
            if($is_check_me){
                $where['id'] = ['neq',$data['id']];
            }
			// 有提交货号检查唯一
            if (model('Goods')->get($where)) {
                $this->error('货号重复');
            }
        } else {
			// 生成唯一货号uniqid 为PHP内置的函数生成唯一字符串
            $data['goods_sn'] = strtoupper('SHOP' . uniqid());
        }
    }
}

?>