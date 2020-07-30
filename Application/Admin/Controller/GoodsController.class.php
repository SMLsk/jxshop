<?php
namespace Admin\Controller;

class GoodsController extends CommonController {

	public function add()
	{
		if(IS_GET){
			//获取所有类型
			$type = D('Type')->select();
			$this->assign('type',$type);
			//获取所有的分类信息
			$categories = D('Category')->getCategoryTree();
			// $this->dumpDie($categories);
			$this->assign('cate',$categories);
			$this->display();
			exit();
		}
		$model = D('Goods');
		$data = $model->create();
		if(!$data){
			$this->error($model->getError());
		}
		$goods_id = $model->add($data);
		if(!$goods_id){
			$this->error($model->getError());
		}
		$this->success('添加成功');
	}
	
	public function index(){
		$model = D('Goods');
		$data = $model->listData();
		// dumpDie($data);
		$this->assign('data',$data);
		$categories = D('Category')->getCategoryTree();
		$this->assign('categories',$categories);
		$this->display();
	}
	
	public function dels(){
		$goods_id = $this->checkIntData();
		$model = D('Goods');
		$res = $model->dels($goods_id);
		if($res === false){
			$this->error($model->getError());
		}
		$this->success('删除成功');
	}
	
	public function edit(){
		if(IS_GET){
			$goods_id = $this->checkIntData();
			$model = D('Goods');
			$info = $model->findOneById($goods_id);
			if(!$info){
				$this->error();
			}
			$info['goods_body'] = htmlspecialchars_decode($info['goods_body']);
			$this->assign('info',$info);
			//获取分类信息
			$categories = D('Category')->getCategoryTree();
			$this->assign('cate',$categories);
			//获取扩展分类
			$ext_cate_ids = M('GoodsCate')->where("goods_id = $goods_id")->select();
			if(!$ext_cate_ids){
				$ext_cate_ids = array(
					array('msg' => 'no data')
				);
			}
			$this->assign('ext_cate_ids',$ext_cate_ids);
			//获取所有类型
			$type = D('Type')->select();
			$this->assign('type',$type);
			//根据商品标识符获取当前商品对应的属性及属性值
			$goodsAttrModel = M('GoodsAttr');
			$attr = $goodsAttrModel->alias('a')->field('a.*,b.attr_name,b.attr_type,b.attr_input_type,b.attr_value')->join('left join jx_attribute b on b.id=a.attr_id')->where('a.goods_id='.$goods_id)->select();
			foreach($attr as $key => $value){
				if($value['attr_input_type'] == 2){
					$attr[$key]['attr_value'] = explode(',',$value['attr_value']);
				}
			}
			foreach($attr as $key => $value){
				$attr_list[$value['attr_id']][] = $value;
			}
			$this->assign('attr',$attr_list);
			//获取商品对应的相册图片信息
			$goods_img_list = M('GoodsImg')->where('goods_id='.$goods_id)->select();
			$this->assign('goods_img_list',$goods_img_list);
			$this->display();
		}else{
			$model = D('Goods');
			$data = $model->create();
			if(!$data){
				$this->error($model->getError());
			}
			$res = $model->update($data);
			if($res === false){
				$this->error($model->getError());
			}
			$this->success('修改成功',U('index'));
		}
		
	}
	
	public function trash(){
		$categories = D('Category')->getCategoryTree();
		$this->assign('categories',$categories);
		$model = D('Goods');
		//调用模型方法获取数据
		$data = $model->listData(1);
		// dump($data);
		// exit();
		$this->assign('data',$data);
		$this->display();
	}
	
	//还原商品
	public function recover(){
		$goods_id = $this->checkIntData();
		$model = D('Goods');
		$res = $model->setStatus($goods_id,0.);
		if($res === false){
			$this->error('还原失败',U('trash'));
		}else{
			$this->success('还原成功',U('trash'));
		}
	}
	
	public function remove(){
		$goods_id = $this->checkIntData();
		$res = D('Goods')->remove($goods_id);
		if($res === false){
			$this->error('删除失败',U('trash'));
		}else{
			$this->success('删除成功',U('trash'));
		}
	}
	
	public function showAttr(){
		$type_id = intval(I('post.type_id'));
		if($type_id <= 0){
			echo '没有数据';exit();
		}
		$data = D('Attribute')->where('type_id='.$type_id)->select();
		foreach($data as $key => $value){
			if($value['attr_input_type'] == 2){
				$data[$key]['attr_value'] = explode(',',$value['attr_value']);
			}
		}
		$this->assign('data',$data);
		$this->display();
	}
	
	public function delImg(){
		$img_id = $this->checkIntData('post.img_id');
		$goodsImgModel = M('GoodsImg');
		$info = $goodsImgModel->where('id='.$img_id)->find();
		if(!$info){
			$this->ajaxReturn(array('status'=>0,'msg'=>'参数错误'));
		}
		unlink($info['goods_img']);
		unlink($info['goods_thumb']);
		$res = $goodsImgModel->where('id='.$img_id)->delete();
		if(!$res){
			$this->ajaxReturn(array('status'=>0,'msg'=>'删除失败'));
		}
		$this->ajaxReturn(array('status'=>1,'msg'=>'OK'));
	}
	
	public function setNumber(){
		if(IS_GET){
			$goods_id = $this->checkIntData('get.id');
			$goodsAttrModel = D('GoodsAttr');
			$attr = $goodsAttrModel->getSingleAttr($goods_id);
			if(!$attr){
				//该商品没有单选属性
				$info = D('Goods')->where('id='.$goods_id)->find();
				$this->assign('info',$info);
				$this->display('nosigle');
				exit;
			}
			$info = M('GoodsNumber')->where('goods_id='.$goods_id)->select();
			if(!$info){
				$info = array('goods_number'=>0);
			}
			$this->assign('info',$info);
			$this->assign('attr',$attr);
			$this->display();
		}else{
			$attr = I('post.attr');
			$goods_number = I('post.goods_number');
			$goods_id = I('post.goods_id');
			if(!$attr){
				// dump($goods_id);exit;
				$res = D('Goods')->where('id='.$goods_id)->setField('goods_number',$goods_number);
				if(!$res){
					$this->error('错误');
				}
				$this->success('设置成功',U('index'));
				exit;
			}
			// dump($_POST);
			//通过循环处理组合，最后数据入库
			foreach($goods_number as $key=>$value){
				// 拼接具体的组合信息
				$tmp = array();
				foreach($attr as $k => $v){
					$tmp[] = $v[$key];
				}
				sort($tmp);
				//将组合的数组格式转换为字符串的格式
				$goods_attr_ids = implode(',',$tmp);
				//实现组合的去重
				if(in_array($goods_attr_ids,$has)){
					unset($goods_number[$key]);
					continue;
				}
				$has[] = $goods_attr_ids;
				$list[] = array(
					'goods_id'=>$goods_id,
					'goods_number'=>$value,
					'goods_attr_ids'=>$goods_attr_ids
				);
			}
			// var_dump($attr,$goods_number,$goods_id,$list);die();
			//删除当前商品及拥有的库存信息
			M('GoodsNumber')->where('goods_id='.$goods_id)->delete();
			//将库存信息入库
			$res = M('GoodsNumber')->addAll($list);
			//计算当前库存总数
			$goods_count = array_sum($goods_number);
			D('Goods')->where('id='.$goods_id)->setField('goods_number',$goods_count);
			if(!$res){
				$this->error('错误');
			}
			$this->success('设置成功',U('index'));
		}
	}
	/* 测试方法 */
	public function testSql(){
		$cate_id = '1';
		$cateModel = D('Category');
		$tree = $cateModel->getChildren($cate_id);
		//将提交的当前分类ID补充到数组中
		$tree[] = $cate_id;
		//将$tree转换为字符串格式
		$children = implode(',',$tree);
		dump(M('GoodsCate')->group('goods_id')->where("cate_id in ($children)")->field('goods_id')->select());
	}
	
	public function testRemove(){
		D('Goods')->remove(141);
	}
	
	public function testU(){
		echo U('remove','id = 1');
	}
}