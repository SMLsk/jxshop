<?php
namespace Admin\Model;

class AdminModel extends CommonModel{
	protected $fields = array('id','username','password','last_visit');
	
	protected $_validate = array(
		array('username','require','用户名必须填写'),
		array('username','','用户名重复',1,'unique'),
		array('password','require','密码必须填写'),
	);
	
	protected $_auto = array(
		array('password','md5',3,'function'),
	);
	
	public function _after_insert($data){
		$admin_role = array(
			'admin_id' => $data['id'],
			'role_id' => I('post.role_id')
		);
		M('AdminRole')->add($admin_role);
	}
	
	public function listData(){
		$totalRows = $this->count();
		$listRows = 3;
		$page = new \Think\Page($totalRows,$listRows);
		$pageStr = $page->show();
		
		$p = I('get.p');
		$list = $this->alias('a')->field('a.*,c.role_name')->join('left join jx_admin_role b on a.id=b.admin_id')->join('left join jx_role c on b.role_id=c.id')->page($p,$listRows)->select();
		return array('list' => $list,'pageStr' => $pageStr);
	}
	
	public function remove($admin_id){
		//开启事务
		$this->startTrans();
		$adminStatus = $this->where('id='.$admin_id)->delete();
		if(!$adminStatus){
			$this->rollback();
			return false;
		}
		
		$roleStatus = M('AdminRole')->where('admin_id='.$admin_id)->delete();
		if(!$roleStatus){
			$this->rollback();
			return false;
		}
		$this->commit();
		return true;
	}
	
	public function findOneById($id){
		return $this->alias('a')->field("a.*,b.role_id")->where('a.id='.$id)->join('left join jx_admin_role b on b.admin_id=a.id ')->find();
	}
	
	public function update(array $data){
		$role_id = intval(I('post.role_id'));
		$this->save($data);
		$res = M('AdminRole')->where('admin_id='.$data['id'])->save(array('role_id'=>$role_id));
	}
	
	public function login($username,$password){
		$userInfo = $this->where("username='$username'")->find();
		if(!$userInfo){
			$this->error = '用户名不存在';
			return false;
		}
		$password = md5($password);
		// dump();die();
		if($password != $userInfo['password']){
			$this->error = '密码错误';
			return false;
		}
		$userInfo['last_visit'] = time();
		// dump($userInfo);
		$res = $this->save($userInfo);
		// dump($this->getLastSql());
		// dump($res);die();
		if(!$res){
			echo '登录失败';
			die();
		}
		cookie('admin',$userInfo,24*60*60);
		return true;
	}
	
}




