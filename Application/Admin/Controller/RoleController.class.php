<?php
namespace Admin\Controller;

class RoleController extends CommonController{
	public function add(){
		if(IS_GET){
			$this->display();
		}else{
			$model = D('Role');
			$data = $model->create();
			if(!$data){
				$this->error($model->getError());
			}
			$res = $model->add();
			if(!$res){
				$this->error($model->getError());
			}
			$this->success('添加成功');
		}
	}
	
	public function index(){
		$model = D('Role');
		$data = $model->listData();
		$this->assign('data',$data);
		$this->display();
	}
	
	public function dels(){
		$role_id = $this->checkIntData();
		$res = D('Role')->remove($role_id);
		if($res === false){
			$this->error('删除失败');
		}
		$this->success('删除成功');
	}
	
	public function edit(){
		if(IS_GET){
			$role_id = $this->checkIntData();
			$model = D('Role');
			$info = $model->findOneById($role_id);
			$this->assign('info',$info);
			$this->display();
		}else{
			$model = D('Role');
			$data = $model->create();
			if(!$data){
				$this->error($model->getError());
			}
			if($data['id'] <= 0){
				$this->error('参数错误');
			}
			$model->save($data);
			$this->success('修改成功',U('index'));
		}
	}
	
	public function disfetch(){
		if(IS_GET){
			$ruleModel = D('Rule');
			$role_id = $this->checkIntData('get.role_id');
			$hasRules = D('RoleRule')->getRules($role_id);
			foreach($hasRules as $value){
				$hasRuleIds[] = $value['rule_id'];
			}
			// $this->dumpDie($hasRuleIds);
			$rules = $ruleModel->getRuleTree();
			$this->assign('rules',$rules);
			$this->assign('hasRules',$hasRuleIds);
			$this->display();
		}else{
			$role_id = $this->checkIntData('get.role_id');
			$rules = I('post.rule');
			
			$res = D('RoleRule')->disfetch($role_id,$rules);
			// $this->dumpDie($res);
			if(!$res){
				$this->error('数据添加失败');
			}
			// $user_info = M('AdminRole')->where('role_id='.$role_id)->select();
			// foreach($user_info as $key => $value){
				// //删除某个用户对应的文件信息
				// S('user_'.value['admin'],null);
			// }
			
			$this->success('数据添加成功',U('index'));
		}
	}
	
	/* //作用就是更新超级管理员用户对应的缓存文件
	public function flushAdmin()
	{
		//获取所有的超级管理员用户
		$user = M('AdminRole')->where('role_id=1')->select();
		//将所有的超级管理员用户对应的缓存文件删除
		foreach ($user as $key => $value) {
			S('user_'.$value['admin_id'],null);
		}
		echo 'ok';
	}  */
	
	/* 测试方法 */
	public function test(){
		// $this->showTable('Role');
		// $model = D('Role');
		// $model->showFields();
		$this->showTable('RoleRule');
		/* $model = D('RoleRule');
		$model->getFields(); */
		$this->showTable('Role');
	}
	
	
}

