<?php
namespace Home\Controller;

class GoodsController extends CommonController{
	public function index(){
		$goods_id = intval(I('get.goods_id'));
		if($goods_id<=0){
			$this->redirect('Index/index');
		}
		$goods = D('Admin/Goods')->where('is_sale=1 and id='.$goods_id)->find();
		if(!$goods){
			$this->redirect('Index/index');
		}
		//如果商品处于促销阶段，价格显示出为促销价格
		if($goods['promotion_price']>0 && $goods['start']<time() && $goods['end']>time()){
			$goods['shop_price'] = $goods['promotion_price'];
		}
		$goods['goods_body'] = htmlspecialchars_decode($goods['goods_body']);
		$this->assign('goods',$goods);
		$pic = M('GoodsImg')->where('goods_id='.$goods_id)->select();
		$this->assign('pic',$pic);
		//获取商品对应的属性信息
		$attr = M('GoodsAttr')->alias('a')->field('a.*,b.attr_name,b.attr_type')->join('left join jx_attribute b on a.attr_id=b.id')->where('a.goods_id='.$goods_id)->select();
		//对获取到的属性进行格式化操作
		foreach($attr as $key => $value){
			if($value['attr_type']==1){
				//唯一属性
				$unique[]=$value;
			}else{
				//单选属性，格式化为三维数组，并且一维下标是使用属性ID
				$single[$value['attr_id']][] = $value;
			}
		}
		$this->assign('unique',$unique);
		$this->assign('single',$single);
		// dumpDie($single);
		$this->display();
	}
}






