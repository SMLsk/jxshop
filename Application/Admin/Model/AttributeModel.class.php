<?php
namespace Admin\Model;

class AttributeModel extends CommonModel{
	protected $fields = array('id','attr_name','type_id','attr_type','attr_input_type','attr_value');
	
	protected $_validate = array(
		array('attr_name','require','属性名必须填写'),
		array('attr_name','','属性名已经存在',1,'unique'),
		array('type_id','require','类型必须选择'),
		array('attr_type','1,2','属性类型只能为单选或者唯一',1,'in'),
		array('attr_input_type','1,2','属性录入方法只能为手工或者列表',1,'in')
	);
	
	public function listData(){
		$totalRows = $this->count();
		$listRows = 3;
		$page = new \Think\Page($totalRows,$listRows);
		$pageStr = $page->show();
		//接受当前所在的页码
		$p = intval(I('get.p'));
		$list = $this->page($p,$listRows)->select();
		//获取类型数据
		$type = D('Type')->select();
		foreach($type as $value){
			$typeInfo[$value['id']] = $value;
		}
		foreach($list as $key => $value){
			$list[$key]['type_name'] = $typeInfo[$value['type_id']]['type_name'];
		}
		return array('list' => $list,'pageStr' => $pageStr);
	}
	
	public function remove($id){
		return $this->where('id='.$id)->delete();
	}
}