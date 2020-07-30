<?php 
namespace Admin\Model;
use Think\Model;
/**
* 公共模型
*/
class CommonModel extends Model
{
	//根据ID获取指定的数据
	public function findOneById($id)
	{
		return $this->where('id='.$id)->find();
	}
	
	/* 测试方法 */
	public function showFields(){
		dump($this->fields);
	}
	
	public function getFields(){
		dump(implode("','",$this->fields));
	}
}

?>