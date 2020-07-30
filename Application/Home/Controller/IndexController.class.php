<?php
namespace Home\Controller;

class IndexController extends CommonController{
    public function index(){
		cookie('user',null);
		echo 'Session information:';
		dump(I('session.'));
		echo 'Cookie information:';
		dump(I('cookie.'));
		$this->assign('is_show',1);
		//获取热卖商品信息
		$goodsModel = D('Admin/Goods');
		$hot = $goodsModel->getRecGoods('is_hot');
		$this->assign('hot',$hot);
		$new = $goodsModel->getRecGoods('is_new');
		$this->assign('new',$hot);
		$rec = $goodsModel->getRecGoods('is_rec');
		$this->assign('rec',$rec);
		$crazy = $goodsModel->getCrazyGoods();
		$this->assign('crazy',$crazy);
		$floor = D('Admin/Category')->getFloor();
		// dumpDie($floor);
		$this->assign('floor',$floor);
		$this->display();
    }
    //测试使用U函数生成的URL地址
    public function testUrl()
    {
    	//第二个参数可以是数组或者字符串格式 作为URL地址上的参数
    	echo U('index','id=2');
    }
}