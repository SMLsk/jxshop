<?php
namespace Home\Controller;
use Think\Controller;
//公共控制器
class CommonController extends Controller{
	public function __construct(){
		parent::__construct();
		//获取分类信息
		$categories = D('Admin/Category')->getCategoryTree();
		// dumpDie($categories);
		$this->assign('cate',$categories);
	}
}