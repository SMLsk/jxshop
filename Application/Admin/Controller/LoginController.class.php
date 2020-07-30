<?php
namespace Admin\Controller;
use Think\Controller;

class LoginController extends Controller{
	private $isVerify = false;
	

	public function login(){
		if(IS_GET){
			$this->assign('isVerify',$this->isVerify);
			$this->display();
		}else{
			if($this->isVerify){
				$captcha = I('post.captcha');
				$config = array(
					'length'	=> 4,
					'fontSize'  => 25,
					'codeSet'   => '1234567890'
				);
				$verify = new \Think\Verify($config);
				$res = $verify->check($captcha);
				if(!$res){
					$this->error('验证码错误',U('login'));
				}
			}
			//比对用户名和密码
			$username = I('post.username');			
			$password = I('post.password');
			$model = D('Admin');
			$res = $model->login($username,$password);
			$this->judgeIfError($res,$model->getError());
			$this->success('登录成功',U('Index/index'));
		}
	}
	
	public function verify(){
		$config = array(
				'length'	=> 4,
				'fontSize'  => 25,
				'codeSet'   => '1234567890',
				'fontttf'   =>  '6.ttf'
		);
		$verify = new \Think\Verify($config);
		$verify->entry();
	}
	
	public function test(){
		// $this->showTable('Admin');
		// dump(__FILE__);
		// echo time();
		dump($_COOKIE);
	}
	
	protected function judgeIfError($res,$message = '操作失败'){
		if(!$res){
			$this->error($message);
		}
	}
}