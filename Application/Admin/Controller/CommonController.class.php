<?php
namespace Admin\Controller;
use Think\Controller;

class CommonController extends Controller {
	public $is_check_rule = true;
	public $user = array();
	
	public function __construct(){
		parent::__construct();
		$admin = cookie('admin');
		if(!$admin){
			$this->error("未登录",U('Login/login'));
		}
		// $this->user = S('user_'.$admin['id']);
		// if(!$this->user){
			// echo 'mysql';
			$this->user = $admin;
			$rule_info = M('AdminRole')->where("admin_id=".$admin['id'])->field('role_id')->find();
			$this->user['role_id'] = $rule_info['role_id'];
			//dump($this->user);
			//获取权限
			$ruleModel = D('Rule');
			if($rule_info['role_id'] == 1){
				//顶级管理员无需判断
				$this->is_check_rule = false;
				$rule_list = $ruleModel->select();
				//dump($rule_list);
				// dump($this->is_check_rule);
				// die();
				// die('OOKK');
			}else{
				//根据角色获取权限
				$rules = M('RoleRule')->where("role_id=".$rule_info['role_id'])->select();
				// dump($rules);
				foreach($rules as $rule){
					$rule_ids[] = $rule['rule_id'];
				}
				$rule_ids = implode(',',$rule_ids);
				$rule_list = $ruleModel->where("id in ($rule_ids)")->select();
			}
			
			foreach($rule_list as $rule){
				$this->user['rules'][] = strtolower($rule['module_name'].'/'.$rule['controller_name'].'/'.$rule['action_name']);
				if($rule['is_show']==1){
					$this->user['menus'][] = $rule;
				}
			}
			// S('user_'.$admin['id'],$this->user);
		// }
		if($this->is_check_rule){
			$this->user['rules'][] = 'admin/index/index';
			$this->user['rules'][] = 'admin/index/top';
			$this->user['rules'][] = 'admin/index/menu';
			$this->user['rules'][] = 'admin/index/main';
			$action = strtoLower(MODULE_NAME .'/'. CONTROLLER_NAME .'/'. ACTION_NAME);
			// var_dump($action,$this->user['rule']);
			if(in_array($action,$this->user['rules'])){
				if(IS_AJAX){
					$this->ajaxReturn(array('status'=>0,'msg'=>'没有权限'));
				}
			}else{
				echo '没有权限';exit();
			}
		}
		
	}
	
	/* 辅助方法 */
	protected function checkIntData($key = 'get.id'){
		$id = intval(I($key));
		if($id <= 0){
			$this->error($id);
		} 
		return $id;
	}
	
	protected function judgeIfError($res,$message = '操作失败'){
		if(!$res){
			$this->error($message);
		}
	}
	
	/* 测试方法 */
	
	public function showFields($table){
		D($table)->showFields();
	}
	
	public function dumpDie($data){
		dump($data);
		die();
	}
	
	public function test(){
		// showTable('Admin');
		// showTable('AdminRole');
		// showTable('Role');
		// showTable('RoleRule');
		// showTable('Rule');
		// showTable('Type');
		// showTable('Attribute');
		// dump($this->user);
		D('Category')->showFields();
		// showTable('Cart');
		// showTable('Goods');
		// showTable('GoodsAttr');
		// showTable('Attribute');
		// showTable('Type');
		// showTable('GoodsImg');
		// showTable('GoodsNumber');
		// showTable('user');
	}
}