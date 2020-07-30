<?php
namespace Home\Model;

class OrderModel extends CommonModel{
	public function order(){
		//获取购物车中商品的信息
		$cateModel = D('Cart');
		$data = $cateModel->getList();
		if(!$data){
			$this->error = '购物车中无商品';
			return false;
		}
		//根据每一个商品做一个库存检查
		foreach($data as $key => $value){
			//具体针对每一个商品检查库存
			$status = $cateModel->checkGoodsNumber($value['goods_id'],$value['goods_count'],$value['goods_attr_ids']);
			if(!$status){
				$this->error = '库存不足';
				return false;
			}
		}
		//想订单总表写入数据
		//计算购物车中商品总价格
		$total = $cateModel->getTotal($data);
		$order = array(
			'user_id' => session('user_id'),
			'addtime' => time(),
			'total_price' => $total['price'],
			'name' => I('post.name'),
			'address' => I('post.address'),
			'tel' => I('post.tel')
		);
		$order_id = $this->add($order);
	}
}