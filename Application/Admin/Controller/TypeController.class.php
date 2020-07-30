<?php
namespace Admin\Controller;

class TypeController extends CommonController{
	public function add(){
		if(IS_GET){
			$this->display();
		}else{
			$model = D('Type');
			$data = $model->create();
			if(empty($data)){
				$this->error($model->getError());
			}
			$res = $model->add($data);
			if(!$res){
				$this->error("数据写入失败");
			}
			$this->success('数据写入成功');
		}
	}
	
	public function index(){
		$model = D('Type');
		$data = $model->listData();
		$this->assign('data',$data);
		$this->display();
	}
	
	public function dels(){
		$type_id = $this->checkIntData('get.type_id');
		$res = D('Type')->remove($type_id);
		if($res === false){
			$this->error('删除失败');
		}
		$this->success('删除成功');
	}
	
	public function edit(){
		if(IS_GET){
			$type_id = $this->checkIntData('get.type_id');
			$model = D('Type');
			$info = $model->findOneById($type_id);
			$this->assign('info',$info);
			$this->display();
		}else{
			$model = D('Type');
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
	
	/* public function disfetch(){
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
			// $user_info = M('AdminType')->where('role_id='.$role_id)->select();
			// foreach($user_info as $key => $value){
				// //删除某个用户对应的文件信息
				// S('user_'.value['admin'],null);
			// }
			
			$this->success('数据添加成功',U('index'));
		}
	} */
	
	/* //作用就是更新超级管理员用户对应的缓存文件
	public function flushAdmin()
	{
		//获取所有的超级管理员用户
		$user = M('AdminType')->where('role_id=1')->select();
		//将所有的超级管理员用户对应的缓存文件删除
		foreach ($user as $key => $value) {
			S('user_'.$value['admin_id'],null);
		}
		echo 'ok';
	}  */
	
	/* 测试方法 */
	public function test(){
		// $this->showTable('Type');
		$model = D('Type');
		$model->showFields();
		/* $model = D('RoleRule');
		$model->getFields(); */
	}
	
	
}

