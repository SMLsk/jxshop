<?php
namespace Admin\Controller;

class AdminController extends CommonController{
	public function add(){
		if(IS_GET){
			$roles = D('Role')->getRoles();
			$this->assign('roles',$roles);
			$this->display();
		}else{
			$model = D('Admin');
			$data = $model->create();
			// dump($data);
			// die();
			if(!data){
				$this->error($model->getError());
			}
			$admin_id = $model->add($data);
			if(!$admin_id){
				$this->error('插入失败');
			}
			$this->success('数据写入成功');
		}
	}
	
	public function index(){
		$model = D('Admin');
		$data = $model->listData();
		$this->assign('data',$data);
		$this->display();
	}
	
	public function dels(){
		$admin_id = $this->checkIntData('get.admin_id');
		$model = D('Admin');
		$res = $model->remove($admin_id);
		if($res === false){
			$this->error('删除失败');
		}
		$this->success('删除成功');
	}
	
	public function edit(){
		if(IS_GET){
			$id = $this->checkIntData('get.admin_id');
			$model = D('Admin');
			$info = $model->findOneById($id);
			// $this->dumpDie($info);
			$this->assign('info',$info);
			// $this->dumpDie($info);
			$roles = D('Role')->getRoles();
			$this->assign('roles',$roles);
			$this->display();
		}else{
			$model = D('Admin');
			$data = $model->create();
			//$this->dumpDie($data);
			if(!$data){
				$this->error($model->getError());
			}
			//$data['role_id'] = $this->checkIntData('post.role_id');
			// $this->dumpDie($data);
			$res = $model->update($data);
			if($res === false){
				$this->error('修改失败');
			}
			$this->success('修改成功');
		}
	}
	
	/* 测试方法 */
	public function test(){
		$this->showTable('Admin');
		echo '<hr/>';
		$this->showTable('AdminRole');
		echo '<hr/>';
		$this->showTable('Role');
		// dump(M('Admin'));
		echo '<hr/>';
		// dump(M('AdminRole'));
		
	}
}