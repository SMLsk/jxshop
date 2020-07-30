<?php
namespace Home\Model;

class UserModel extends CommonModel{
	public function regist($username,$password){
		//检查用户名是否可用
		$info = $this->where("username='$username'")->find();
		if($info){
			$this->error = 'UserName Repetition';
			return false;
		}
		//生成盐
		$salt = rand(100000,999999);
		//生成双重MD5之后的密码
		$db_password = md5(md5($password).$salt);
		$data = array(
			'username' => $username,
			'password' => $db_password,
			'salt'	   => $salt
		);
		return $this->add($data);
	}
	
	public function login($username,$password){
		//检查用户名是否存在
		$info = $this->where("username='$username'")->find();
		if(!$info){
			$this->error = 'The username doesn\'t exist';
			return false;
		}
		$password = md5(md5($password).$info['salt']);
		if($password != $info['password']){
			$this->error = 'Password Error';
			return false;
		}
		//保存用户的登录状态
		session('user',$info);
		session('user_id',$info['id']);
		//Move the data in the cookie to the database.
		D('Cart')->cookie2db();
		return true;
	}
}