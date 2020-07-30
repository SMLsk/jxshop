<?php
namespace Admin\Model;

class RoleRuleModel extends CommonModel{
	protected $fields = array('id','role_id','rule_id');
	
	public function disfetch($role_id,$rules){
		$this->where("role_id=".$role_id)->delete();
		$data = array();
		foreach($rules as $value){
			$data[] = array('rule_id' => $value,'role_id' => $role_id);
		}
		return $this->addAll($data);
	}
	
	public function getRules($role_id){
		return $this->where('role_id='.$role_id)->field('rule_id')->select();
	}
}