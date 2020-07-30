<?php
namespace Home\Controller;

class CartController extends CommonController{
	public function addCart(){
		// dumpDie(I('post.'));
		$goods_id = intval(I('post.goods_id'));
		$goods_count = intval(I('post.goods_count'));
		$attr = I('post.attr');
		//实例话模型对象调用方法实现数据写入
		$model = D('Cart');
		$res = $model->addCart($goods_id,$goods_count,$attr);
		if(!$res){
			$this->error($model->getError());
		}
		$this->success("Successfull Writing");
	}
	
	//实现购物车列表显示功能
	public function index(){
		$model = D('Cart');
		//获取购物车中具体的商品信息
		$data = $model->getList();
		$this->assign('data',$data);
		//计算当前购物车总金额
		$total = $model->getTotal($data);
		$this->assign('total',$total);
		$this->display();
	}
	
	//实现删除
	public function dels(){
		$goods_id = intval(I('get.goods_id'));
		$goods_attr_ids = I('get.goods_attr_ids');
		// var_dump($goods_id,$goods_attr_ids);
		if(D('Cart')->dels($goods_id,$goods_attr_ids)){
			$this->success('删除成功');
			die();
		}
		$this->error('删除失败');
	}
	
	public function updateCount(){
		$goods_id = intval(I('post.goods_id'));
		$goods_attr_ids = I('post.goods_attr_ids');
		$goods_count = intval(I('post.goods_count'));
		// die($goods_id);
		$res = D('Cart')->updateCount($goods_id,$goods_attr_ids,$goods_count);
		if($res){
			$this->ajaxReturn(array('status'=>1,'msg'=>'OKK'));
		}
	}
	
}








