<?php
    namespace app\admin\controller;
    use think\Db;
    use think\Request;

   


    class Category extends Common
    {
        public function edit(Request $request)
        {
            $model = model('Category');
            //获取要修改的分类id
            $cate_id = input('id');
            if ($request->isGet()) {
                //获取要修改的分类的原因信息
                $info = $model->get($cate_id);
                //获取所有的分类信息
                $category = $model->getTree();
                return $this->fetch('edit', ['info' => $info, 'category' => $category]);
            }
            //调用自定义的数据修改数据
            $result = $model->editCategory(input());
            if($result === FALSE){
                $this->error($model->getError());
            }
            $this->success('ok');

        }
        public function remove()
        {
            //分类的删除
            $model = model('Category');
            //调用自定义的模型方法删除分类ID
            $result = $model -> remove(input('id'));
            if($result === FALSE){
                $this->error($model->getError());
            }
            $this->success('ok');
        }
        public function index(){
            //分类的列表显示
            $data = model('Category')->getTree();
            $this->assign('data',$data);
            return $this->fetch();
        }
        public function add(Request $request)
        {
            if($request -> isGet()){
            //使用模型对象调用自定义的方法获取已经格式化后的数据
            $category = model('Category')->getTree();
            $this -> assign('category',$category);
            return $this -> fetch();
            }

            //post请求的表单提交
            $data = $request -> post();
            $result = Db::name('category')->insert($data);
            if ($result === false) {
                $this->error('写入错误', 'add');
            }
            $this->success('写入成功', 'index');

        }

    }

    