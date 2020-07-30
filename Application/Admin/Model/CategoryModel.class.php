<?php 
namespace Admin\Model;

/**
* 分类模型
*/
class CategoryModel extends CommonModel
{
	//自定义字段
	//protected $fields=array('id','cname','parent_id','isrec');
	//自动验证
	protected $_validate=array(
		array('cname','require','分类名称必须填写'),
	);
	//获取某个分类的子分类
	public function getChildren($id){
		//先获取所有的分类信息
		$data = $this->select();
		//对获取的信息进行格式化
		$list = $this->getTree($data,$id,1,false);
		foreach($list as $key => $value){
			$tree[] = $value['id'];
		}
		return $tree;
	}
	
	//获取格式化之后的数据
	public function getCategoryTree($id=0)
	{
		//先获取所有的分类信息
		$data = $this->select();
		//在对获取的信息进行格式化
		$list = $this->getTree($data,$id);
		return $list;
	}
	//格式化分类信息
	public function getTree($data,$id=0,$lev=1,$iscache=true)
	{
		static $list = array();
		//根据参数决定是否需要重置
		if(!$iscache){
			$list = array();
		}
		foreach ($data as $key => $value) {
			if($value['parent_id']==$id){
				$value['lev']=$lev;
				$list[]=$value;
				//使用递归的方式获取分类下的子分类
				$this->getTree($data,$value['id'],$lev+1);
			}
		}
		return $list;
	}
	//删除分类
	public function dels($id)
	{
		//如果删除的分类下有子分类不容许删除
		$result = $this->where('parent_id='.$id)->find();
		if($result){
			return false;
		}
		return $this->where('id='.$id)->delete();
	}
	public function update($data)
	{
		// dump($data);exit();
		// 需要过滤掉设置父分类为自己或者自己下的子分类
		// 1、根据要修改的分类的标识 获取到自己下的所有的子分类
		$tree = $this->getCategoryTree($data['id']);
		//将自己添加到不能修改的数组中
		$tree[]=array('id'=>$data['id']);
		// dump($tree);exit();
		// 2、根据提交的parent_id的值 判断如果等于当前修改的分类ID或者是当前分类下的所有子分类的ID不容许修改
		foreach ($tree as $key => $value) {
			if($data['parent_id']==$value['id']){
				$this->error='不能设置子分类为父分类';
				return false;
			}
		}
		
		return $this->save($data);
	}
	
	public function getFloor(){
		$data = $this->where('parent_id=0')->select();
		foreach($data as $key => $value){
			//获取二级分类信息
			$data[$key]['son'] = $this->where('parent_id='.$value['id'])->select();
			//获取推荐的二级分类信息
			$data[$key]['recson']=$this->where('isrec=1 and parent_id='.$value['id'])->select();
			//根据每一个楼层推荐的二级分类信息获取对应的商品数据
			foreach($data[$key]['recson'] as $k => $v){
				//$v代表队就是每一个推荐分类信息
				$data[$key]['recson'][$k]['goods'] = $this->getGoodsByCateId($v['id']);
			}
		}
		return $data;
	}
	
	public function getGoodsByCateId($cate_id,$limit = 8){
		$children = $this->getChildren($cate_id);
		$children[] = $cate_id;
		$children = implode(',',$children);
		$goods = D('Goods')->where("is_del=1 and cate_id in ($children)")->limit($limit)->select();
		return $goods;
	}
}

?>