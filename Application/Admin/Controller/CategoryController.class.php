<?php
namespace Admin\Controller;

class CategoryController extends CommonController {
	//实现分类的添加
	public function add()
	{
		if(IS_GET){
			//获取格式化之后的分类信息
			$model= D('Category');
			$categories = $model->getCategoryTree();
			//将信息赋值给模板
			$this->assign('categories',$categories);
			$this->display();
		}else{
			//数据入库
			$model = D('Category');
			//创建数据
			$data = $model->create();
			if(!$data){
				$this->error($model->getError());
			}
			$insertid = $model->add($data);
			if(!$insertid){
				$this->error('数据写入失败');
			}
			$this->success('写入成功');
		}
	}
	//分类的列表显示
	public function index()
	{
		$model = D('Category');
		$categories = $model->getCateTree();
		$this->assign('categories',$categories);
		$this->display();
	}
	//实现商品分类的删除
	public function dels()
	{
		$id = $this->checkIntData();
		$model = D('Category');
		//调用模型中的删除方法实现删除操作
		$res = $model->dels($id);
		if($res===false){
			$this->error('删除失败');
		}
		$this->success('删除成功');
	}

	public function edit()
	{
		if(IS_GET){
			//显示要编辑的分类信息
			$id = $this->checkIntData();
			//根据ID参数获取该分类的信息
			$model = D('Category');
			$info = $model ->findOneById($id);
			$this->assign('info',$info);
			//获取所有的分类信息
			$categories = $model->getCateTree();
			$this->assign('categories',$categories);
			$this->display();
		}else{
			$model = D('Category');
			$data = $model->create();
			if(!$data){
				$this->error($model->getError());
			}
			$res = $model ->update($data);
			if($res === false){
				$this->error($model->getError());
			}
			$this->success('修改成功');
		}
	}
}