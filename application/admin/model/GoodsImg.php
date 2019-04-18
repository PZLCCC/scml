<?php 
namespace app\admin\model;

use think\Model;
use think\Db;
/**
 * 
 */
class GoodsImg extends Model
{
    public function insertImages($goods_id)
    {
        $list = [];
        $upload_base = config('upload_base');//从配置项读取出上传的根目录
		// 获取file对象
        $files = request()->file('pics');
        foreach ($files as $file) {
            $info = $file->validate(['ext' => 'jpg,png'])->move($upload_base);
            if (!$info) {
				// 文件上传有错误
                continue;
            }
			// 3、提取上传之后的文件地址
            $goods_img = $upload_base . '/' . $info->getSaveName();
			// 4、更换地址中的\为/
            $goods_img = str_replace('\\', '/', $goods_img);
			
			// 根据上次的图片生成缩略图
			// 打开图片获取对象
            $img = \think\Image::open($goods_img);
			// 计算生成缩略图的保存地址
			// 保存的地址与原图存储在同一个目录文件名称在原图的基础上增加thumb_前缀
            $goods_thumb = $upload_base . '/' . date('Ymd') . '/thumb_' . $info->getFileName();
			// 生成缩略图
            $img->thumb(200, 200)->save($goods_thumb);
			// 将本地的图片转移到资源服务器下
            img_to_cdn($goods_img);//转移原图
            img_to_cdn($goods_thumb);//转移缩略图
            $list[] = [
                'goods_id' => $goods_id,
                'goods_img' => $goods_img,
                'goods_thumb' => $goods_thumb
            ];
        }
        if ($list) {
            $this->saveAll($list);
        }
    }
}