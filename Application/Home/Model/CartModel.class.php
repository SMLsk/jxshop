<?php
namespace Home\Model;

class CartModel extends CommonModel{
	protected $fields =  array('id','user_id','goods_id','goods_attr_ids','goods_count');
	
	public function addCart($goods_id,$goods_count,$attr){
		
		//Sort attribute from small to large to facilitate later inventory checking
		sort($attr);
		//Convert the attribute information from the array to a string.
		$goods_attr_ids = $attr?implode(',',$attr):'';
		//Achieve inventory check
		$res = $this->checkGoodsNumber($goods_id,$goods_count,$goods_attr_ids);
		if(!$res){
			$this->error = 'Insufficient inventory';
			return false;
		}
		//Get the user_id
		$user_id = session('user_id');
		// dumpDie(I('session.'));
		if($user_id){
			//The user is logged in
			//Determine if the item is present in the database based on the data to be wrriten.if so,update the corresponding quantity directly.
			$map = array(
				'user_id' => $user_id,
				'goods_id' => $goods_id,
				'goods_attr_ids' => $goods_attr_ids
			);
			$info = $this->where($map)->find();
			if($info){
				//The data already exists,the corresponding amount should be updated.
				$res = $this->where($map)->setField('goods_count',$goods_count+intval($info['goods_count']));
			}else{
				//This means that the data does not exist and can be written directly to the database
				$map['goods_count'] = $goods_count;
				$res = $this->add($map);
			}
			if(!$res){
				//insertion failed
				$this->error = "insertion failed";
				return false;
			}
			return true;
		}else{
			//If the user isn't logged in,the corresponding action is manipulate the cookie's data.
			//when the shopping cart data is added to the cookie,the name of the cart is used as the index of cookie,and the serialization opration is used to convert the data from array into a string.
			$user = unserialize(cookie('cart'));
			// dumpDie($user);
			//Determine the presence of new additions
			//First,splice the corresponding index
			$key = $goods_id.'-'.$goods_attr_ids;
			if(array_key_exists($key,$cart)){
				$cart[$key]+=$goods_count;
			}else{
				//This indicates the added item does not exist
				$cart[$key] = $goods_count;
			}
			//After processing,you need to write the latest data to the cookie again.
			cookie('cart',serialize($cart));
			return true;
			// dump($cart);
			// dumpDie(I('cookie.'));
		}
	}
	
	public function checkGoodsNumber($goods_id,$goods_count,$goods_attr_ids){
		// var_dump($goods_id,$goods_count,$goods_attr_ids);die();
		//Checking the total inventory
		$goods = D('Admin/Goods')->findOneById($goods_id);
		if($goods['goods_number']<$goods_count){
			//This represents a shortage stock
			return false;
		}
		//Check the inventory of corresponding attribute combination according to the single selected attribute
		if($goods_attr_ids){
			$where = "goods_id=$goods_id and goods_attr_ids='$goods_attr_ids'";
			$number = M('GoodsNumber')->where($where)->find();
			if(!$number || $number['goods_number']<$goods_count){
				//This means we don't have enough inventory.
				return false;
			}
		}
		return true;
	}
	
	//Move the data in the cookie to the database
	public function cookie2db(){
		//Gets the data of shopping cart in the cookie.
		$cart = unserialize(cookie('cart'));
		//Gets the id of the current user.
		$user_id = session('user_id');
		if(!$user_id){
			return false;
		}
		foreach($cart as $key => $value){
			//First,separate out the combination of the item id and attribute value corresponding to the current cookie index.
			$tmp = explode('-',$key);
			$map = array(
				'user_id' => $user_id,
				'goods_id' => $tmp[0],
				'goods_attr_ids' => $tmp[1]
			);
			
			$info = $this->where($map)->find();
			if($info){
				//The data already exists,the corresponding amount should be updated.
				$this->where($map)->setField('goods_count',$value+intval($info['goods_count']));
			}else{
				//The current data information does not exist and can be written directly to the database.
				$map['goods_count'] = $value;
				$this->add($map);
			}
		}
		//Empty the data in the current cookie.
		cookie('cart',null);
	}
	
	//Gets the detailed	data for the item in the shopping cart 
	public function getlist(){
		//1、获取当前购物车中对应的信息
		$user_id = session('user_id');
		if($user_id){
			//表示用户已经登录，可以从数据库中获取购物车的数据
			$data = $this->where('user_id='.$user_id)->select();
		}else{
			//表示未登录，直接从cookie中获取对应的购物车数据
			$cart = unserialize(cookie('cart'));
			//将没有登录的购物车数据转换为数据库中的格式
			foreach($cart as $key => $value){
				$tmp = explode('-',$key);
				$data[] = array(
					'goods_id' => $tmp[0],
					'goods_attr_ids' => $tmp[1],
					'goods_count' => $value
				);
			}
		}
		//2、根据购物车中的商品ID获取商品信息
		$goodsModel = D('Admin/Goods');
		foreach($data as $key => $value){
			//获取具体的商品信息
			$goods = $goodsModel->where('id='.$value['goods_id'])->find();
			//根据商品是否处于促销状态设置价格
			if($goods['promotion_price']>0 && $goods['start']<time() && $goods['end']>time()){
				//处于促销状态因此设置对应的shop_price为促销价格
				$goods['shop_price'] = $goods['promotion_price'];
			}
			$data[$key]['goods']=$goods;
			// var_dump($data);
			//3、根据商品对应的属性值的组合获取对应的属性名称跟属性值
			if($value['goods_attr_ids']){
				//获取商品的属性信息
				$attr = M('GoodsAttr')->alias('a')->join('left join jx_attribute b on a.attr_id=b.id')->field('a.attr_values,b.attr_name')->where("a.id in ({$value['goods_attr_ids']})")->select();
				$data[$key]['attr'] = $attr;
			}
		}
		// dumpDie($data);
		return $data;
	}
	
	public function getTotal($data){
		//初始商品个数和总金额均为0
		$count = $price = 0;
		foreach($data as $key => $value){
			$count+=intval($value['goods_count']);
			$price+=intval($value['goods_count'])*intval($value['goods']['shop_price']);
		}
		return array('count'=>$count,'price'=>$price);
	}
	
	public function dels($goods_id,$goods_attr_ids){
		$goods_attr_ids = $goods_attr_ids?$goods_attr_ids:'';
		
		$user_id = session('user_id');
		if($user_id){
			$where = "user_id=$user_id and goods_id=$goods_id and goods_attr_ids='$goods_attr_ids'";
			
			if($this->where($where)->delete()){
				return true;
			}
		}else{
			$cart = unserialize(cookie('cart'));
			//手动拼接当前商品对应的Key信息
			$key = $goods_id.'-'.$goods_attr_ids;
			unset($cart[$key]);
			//将最新的数据再次写入到cookie中
			cookie('cart',$cart);
			return true;
		}
	}
	
	public function updateCount($goods_id,$goods_attr_ids,$goods_count){
		//当$goods_count的值小于0时不进行更新
		if($goods_count){
			return false;
		}
		$goods_attr_ids = $goods_attr_ids?$goods_attr_ids:'';
		$user_id = session('user_id');
		if($user_id){
			$where = "user_id=$user_id and goods_id=$goods_id and goods_attr_ids='$goods_attr_ids'";
			$res = $this->where($where)->setField('goods_count',$goods_count);
			if(!$res){
				return false;
				
			}
		}else{
			$cart = unserialize(cookie('cart'));
			$key = $goods_id.'-'.$goods_attr_ids;
			$cart[$key] = $goods_count;
			$res = cookie('cart',$cart);
			if(!$res){
				return false;
			}
		}
		return true;
	}
	
	/* testActions */
	public function getFields(){
		dump(implode("','",$this->fields));
	}
}






