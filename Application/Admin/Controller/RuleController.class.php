<?php
namespace Admin\Controller;

class RuleController extends CommonController {
	//实现分类的添加
	public function add()
	{
		if(IS_GET){
			//获取格式化之后的分类信息
			$model= D('Rule');
			$rules = $model->getRuleTree();
			//将信息赋值给模板
			$this->assign('rules',$rules);
			$this->display();
		}else{
			//数据入库
			$model = D('Rule');
			//创建数据
			$data = $model->create();
			if(!$data){
				$this->error($model->getError());
			}
			$insertid = $model->add($data);
			if(!$insertid){
				$this->error('数据写入失败');
			}
			$this->success('写入成功',U('index'));
		}
	}
	
	//分类的列表显示
	public function index(){
		$model= D('Rule');
		$rules = $model->getRuleTree();
		//将信息赋值给模板
		$this->assign('rules',$rules);
		$this->display();
	}
	
	//实现商品分类的删除
	public function dels()
	{
		$id = $this->checkIntData();
		$model = D('Rule');
		//调用模型中的删除方法实现删除操作
		$res = $model->dels($id);
		if($res===false){
			$this->error('删除失败');
		}
		$this->success('删除成功',U('index'));
	}

	public function edit()
	{
		if(IS_GET){
			//显示要编辑的分类信息
			$id = $this->checkIntData();
			//根据ID参数获取该分类的信息
			$model = D('Rule');
			$info = $model ->findOneById($id);
			$this->assign('info',$info);
			//获取所有的分类信息
			$rules = $model->getRuleTree();
			//将信息赋值给模板
			$this->assign('rules',$rules);
			$this->display();
		}else{
			$model = D('Rule');
			$data = $model->create();
			if(!$data){
				$this->error($model->getError());
			}
			$res = $model ->update($data);
			if($res === false){
				$this->error($model->getError());
			}
			$this->success('修改成功',U('index'));
		}
	}
	
	public function test(){
		$model = D('Rule');
		$model->getFields();
	}
}