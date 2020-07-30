<?php
namespace Home\Controller;

class OrderController extends CommonController{
	public function check(){
		//判断用户是否登录
		$this->checkLogin();
		$model = D('Cart');
		//获取当前购物车中具体的商品信息
		$data = $model->getList();
		$this->assign('data',$data);
		//计算当前购物车总金额
		$total = $model->getTotal($data);
		$this->assign('total',$total);
		$this->display();
	}
	
	public function checkLogin(){
		$user_id = session('user_id');
		if(!$user_id){
			$this->error('请先登录',U('User/login'));
		}
	}
	
	public function order(){
		$model = D('Order');
		$res = $model->order();
		if(!$res){
			$this->error($model->getError());
		}
		echo 'OK';
	}
}





