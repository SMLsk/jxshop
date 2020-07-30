<?php
namespace Admin\Controller;

class AttributeController extends CommonController{
	protected function model(){
		if(!$this->_model){
			$this->_model = D('Attribute');
		}
		return $this->_model;
	}
	
	public function add(){
		if(IS_GET){
			$type = D('Type')->select();
			$this->assign('type',$type);
			$this->display();
		}else{
			$data = $this->model()->create();
			if(!$data){
				$this->error($this->model()->getError());
			}
			$this->model()->add($data);
			$this->success('写入成功');
		}
	}
	
	public function index(){
		$data = $this->model()->listData();
		$this->assign('data',$data);
		$this->display();
	}
	
	public function edit(){
		if(IS_GET){
			$id = $this->checkIntData('get.attr_id');
			$info = $this->model()->findOneById($id);
			$this->assign('info',$info);
			$type = D('Type')->select();
			$this->assign('type',$type);
			$this->display();
		}else{
			$data = $this->model()->create();
			if(!$data){
				$this->error($this->model()->getError());
			}
			$this->model()->save($data);
			$this->success('修改成功',U('Attribute/index'));
		}
	}
	
	public function dels(){
		$id = $this->checkIntData('get.attr_id');
		$res = $this->model()->remove($id);
		if($res === false){
			$this->error('删除失败');
		}
		$this->success('删除成功',U('index'));
	}
	
	public function test(){
		// $model = D('Attribute');
		// $model->getFields();
	}
}