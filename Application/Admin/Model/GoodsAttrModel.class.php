<?php
namespace Admin\Model;

class GoodsAttrModel extends CommonModel{
	protected $fields = array('id','goods_id','attr_id','attr_values');
	
	public function insertAttr($attr,$goods_id){
		$this->where('goods_id='.$goods_id)->delete();
		foreach($attr as $key => $value){
			foreach($value as $val){
				$attr_list[] = array(
					'goods_id' => $goods_id,
					'attr_id'  => $key,
					'attr_values' => $val
				);
			}
		}
		// dump($attr_list);exit();
		$res = $this->addAll($attr_list);
		if(!$res){
			echo"属性写入失败";
			die();
		}
	}
	
	public function getSingleAttr($goods_id){
		$data = $this->alias('a')->join('left join jx_attribute b on a.attr_id=b.id')->field('a.*,b.attr_name,b.attr_type,b.attr_input_type,b.attr_value')->where('a.goods_id='.$goods_id.' and b.attr_type=2')->select();
		
		foreach($data as $key => $value){
			$list[$value['attr_id']][] = $value;
		}
		// dumpDie($list);
		return $list;
	}
}










