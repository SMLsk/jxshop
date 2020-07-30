<?php 
namespace Admin\Model;

/**
* 分类模型
*/
class GoodsModel extends CommonModel
{

	//自定义字段
	protected $fields=array('id','goods_name','goods_sn','cate_id','market_price','shop_price','goods_img','goods_thumb','goods_body',
	    'is_hot','is_rec','is_new','addtime','isdel','is_sale','type_id','goods_number','promotion_price','start','end');
	//自定义自动验证
	protected $_validate=array(
		array('goods_name','require','商品名称必须填写',1),
		array('cate_id','checkCategory','分类必须填写',1,'callback'),
		array('market_price','currency','市场价格格式不对'),
		array('shop_price','currency','本店价格格式不对'),
	);
	//对分类进行验证
	public function checkCategory($cate_id)
	{
		$cate_id = intval($cate_id);
		if($cate_id>0){
			return true;
		}
		return false;
	}
	//使用TP的钩子函数
	public function _before_insert(&$data)
	{
	    //实现关于促销商品的格式化操作
	    if($data['promotion_price']>0){
	        //设置商品为促销商品
	        $data['start'] = strtotime($data['start']);
	        $data['end'] = strtotime($data['end']);
	    }else{
	        $data['promotion_price'] = 0.00;
	        $data['start'] = 0;
	        $data['end'] = 0;
	    }
		//添加时间
		$data['addtime'] = time();

		//处理货号
		if(!$data['goods_sn']){
			//没有题号货号自动生成
			$data['goods_sn'] = 'JX'.uniqid();
		}else{
			//有提交货号
			$info = $this->where('goods_sn='.$data['goods_sn'])->find();
			if($info){
				$this->error='货号重复';
				return false;
			}
		}
		$res = $this->uploadImg();
		if(!empty($res)){
			$data['goods_img'] = $res['goods_img'];
			$data['goods_thumb'] = $res['goods_thumb'];
		}
	}
	
	public function _after_insert($data){
		$goods_id = $data['id'];
		//接受提交是扩展分类
		$ext_cate_id = I('post.ext_cate_id');
		D('GoodsCate')->insertExtCate($ext_cate_id,$goods_id);
		
		//属性入库
		$attr = I('post.attr');
		D('GoodsAttr')->insertAttr($attr,$goods_id);
		//实现商品相册图片上传以及入库
		//1、将商品图片上传释放
		unset($_FILES['goods_img']);
		//商品相册图片批量上传
		$upload = new \Think\Upload();
		// dumpDie($_FILES);
		//进行上传，未传入参数则上传的文件为$_FILE数组
		$info = $upload->upload();
		// dumpDie($info);
		if(!$info){
			$this->error = $upload->getError();
		}
		foreach($info as $key => $value){
			//获取上传之后的图片地址
			$goods_img = 'Uploads/' . $value['savepath'] . $value['savename'];
			//根据上传的图片进行缩略图的制作
			$img = new \Think\Image();
			//打开图片
			$img->open($goods_img);
			//制作缩略图
			$goods_thumb = 'Uploads/' . $value['savepath'] . 'thumb_' .$value['savename'];
			$img->thumb(100,100)->save($goods_thumb);
			$list[] = array(
				'goods_id' => $goods_id,
				'goods_img' => $goods_img,
				'goods_thumb' => $goods_thumb,
			);
		}
		if($list){
			$img_res = M('GoodsImg')->addAll($list);
		}
		if(!$img_res){
			echo '相册上传失败';die();
		}
	}
	
	public function listData($isdel = 0){
		
		$where = 'isdel = '.$isdel;
		/* 获取子分类 */
		$cate_id = intval(I('get.cate_id'));
		if($cate_id){
			//根据当前分类ID获取子分类
			$cateModel = D('Category');
			$tree = $cateModel->getChildren($cate_id);
			//将提交的当前分类ID补充到数组中
			$tree[] = $cate_id;
			//将$tree转换为字符串格式
			$children = implode(',',$tree);
			
			//获取扩展分类的商品ID
			$ext_goods_id = M('GoodsCate')->group('goods_id')->where("cate_id in ($children)")->field('goods_id')->select();
			if(!empty($ext_goods_id)){
				foreach ($ext_goods_id as $key => $value){
					$goods_ids[] = $value['goods_id'];
				}
				$goods_ids = implode(',',$goods_ids);
			}
			//组合where子句
			if(!$goods_ids){
				//没有商品的扩展分类满足条件
				$where .= " AND cate_id in ($children)";
			}else{
				$where .= " AND (cate_id in ($children) OR id in ($goods_ids))";
			}
		}
		
		/* 接受提交的推荐状态 */
		$intro_type = I('get.intro_type');
		if($intro_type){
			//限制只能使用此三个推荐作为条件
			if($intro_type == 'is_new' || $intro_type == 'is_rec' || $intro_type == 'is_hot'){
				$where .= " AND $intro_type = 1";
			}
		}
		
		/* 接受上下架 */
		$is_sale = intval(I('get.is_sale'));
		if($is_sale == 1){
			//表单提交的1表示上架
			$where .= " AND is_sale = 1";
		}elseif($is_sale == 2){
			$where .= " AND is_sale = 0";
		}
		
		/* 接受关键词搜索 */
		$keyword = I('get.keyword');
		if($keyword){
			$where .= " AND goods_name like '%$keyword%'";
		}
		
		/* 分页操作 */
		//定义每页显示的数据条数
		$pageSize = 3;
		//获取数据总数
		$count = $this->where($where)->count();
		//计算出分页导航
		$page = new \Think\Page($count,$pageSize);
		$show = $page->show();
		//获取当前的页码
		$p = intval(I('get.p'));
		
		/* 获取具体数据 */
		$data = $this->where($where)->page($p,$pageSize)->select();
		
		return array('data' => $data,'pageStr' => $show);
		// dump($data);
		// exit();
	}

	public function dels($id){
		return $this->where("id = $id")->setField('isdel',1);
	}
	
	public function uploadImg(){
		if(!isset($_FILES['goods_img']) || ($_FILES['goods_img']['error']!=0)){
			return false;
		}
		//实现图片上传
		$upload = new \Think\Upload();
		$info = $upload->uploadOne($_FILES['goods_img']);
		if(!$info){
			$this->error = $upload->getError();
		}
		$goods_img = 'Uploads/' . $info['savepath'] . $info['savename'];
		//根据上传的图片制作缩略图
		$img = new \Think\Image;
		//打开图片
		$img->open($goods_img);
		//制作缩略图
		$goods_thumb = 'Uploads/' . $info['savepath'] . 'thumb_'.$info['savename'];
		$img->thumb(450,450)->save($goods_thumb);
		
		return array('goods_img' => $goods_img,'goods_thumb' => $goods_thumb);
	}
	
	public function update($data){
		// dump($data);
		// die();
	    //实现关于促销商品的格式化操作
	    if($data['promotion_price']>0){
	        //设置商品为促销商品
	        $data['start'] = strtotime($data['start']);
	        $data['end'] = strtotime($data['end']);
	    }else{
	        $data['promotion_price'] = 0.00;
	        $data['start'] = 0;
	        $data['end'] = 0;
	    }
		$goods_id = $data['id'];
		//货号问题
		$goods_sn = $data['goods_sn'];
		if(empty($goods_sn)){
			//没有提交货号
			$data['goods_sn'] = 'JX' . uniqid();
		}else{
			$res = $this->where("goods_sn = '$goods_sn' AND id != $goods_id")->find();
			if($res){
				$this->error = '货号重复';
				return false;
			}
			
		}
		
		//解决扩展分类的问题
		//删除之前的扩展分类
		$extCateModel = M('GoodsCate');
		$extCateModel->where("goods_id = $goods_id")->delete();
		//将最新的扩展分类写入数据
		//接受提交的扩展分类
		$ext_cate_id = I('post.ext_cate_id');
		D('GoodsCate')->insertExtCate($ext_cate_id,$goods_id);
		//修改图片
		$res = $this->uploadImg();
		if(!empty($res)){
			$data['goods_img'] = $res['goods_img'];
			$data['goods_thumb'] = $res['goods_thumb'];
		}
		//属性修改
		$attr = I('post.attr');
		$goodsAttrModel = D('GoodsAttr');
		$goodsAttrModel->where('goods_id='.$goods_id)->delete();
		$goodsAttrModel->insertAttr($attr,$goods_id);
		
		//实现追加图片
		unset($_FILES['goods_img']);
		$upload = new \Think\Upload();
		$info = $upload->upload();
		if(!$info){
			dumpDie('图片上传失败');
		}
		foreach($info as $value){
			$goods_img = 'Uploads/' . $value['savepath'] . $value['savename'];
			// dumpDie($goods_img);
			$img = new \Think\Image();
			$img->open($goods_img);
			$goods_thumb = 'Uploads/' . $value['savepath'] . 'thumb_' . $value['savename'];
			$img->thumb(100,100)->save($goods_thumb);
			$list[] = array(
				'goods_id' => $goods_id,
				'goods_img' => $goods_img,
				'goods_thumb' => $goods_thumb,
			);
		}
		if(!empty($list)){
			$img_res = M('GoodsImg')->addAll($list);
		}
		if(!$img_res){
			echo '相册上传失败';die();
		}
		
		return $this->save($data);
	}
	
	public function setStatus($goods_id,$isdel = 1){
		return $this->where("id = $goods_id")->setField('isdel',$isdel);
	}
	
	public function remove($goods_id){
		$goods_info = $this->findOneById($goods_id);
		if(empty($goods_info)){
			return false;
		}
		unlink($goods_info['goods_img']);
		unlink($goods_info['goods_thumb']);
		//删除商品的扩展分类
		D('GoodsCate')->where('goods_id = '. $goods_id)->delete();
		//删除商品的基本信息
		$this->where('id = '.$goods_id)->delete();
		return true;
	}
	
	public function getRecGoods($type){
		return $this->where("is_sale=1 and $type=1")->limit(5)->select();
	}
	
	public function getCrazyGoods(){
		return $this->where("is_sale=1 and promotion_price>0 and start<".time()." and end>".time())->limit(5)->select();
	}
}
	
	
	
	
	
	
	