<?php
namespace Admin\Model;

class TypeModel extends CommonModel{
	protected $fields = array('id','type_name');
	
	protected $_validate = array(
		array('type_name','require','类型名必须填写'),
		array('type_name','','类型名已存在',1,'unique'),
	);
	
	public function listData(){
		//进行分页
		//获取数据总数
		$count = $this->count();
		//设置每页数据条数
		$pageSize = 3;		
		//获取分页导航
		$page = new \Think\Page($count,$pageSize);
		$show = $page->show();
		//获取当前分页
		$pageNow = intval(I('get.p'));
		//获取全部数据
		$list = $this->page($pageNow,$pageSize)->select();
		
		return array('list' => $list,'pageStr' => $show);
	}
	
	public function remove($type_id){
		return $this->where("id = $type_id")->delete();
	}

	public function getRoles(){
		return $this->select();
	}
}