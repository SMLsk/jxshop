<?php
namespace Home\Controller;

class UserController extends CommonController{
	public function regist(){
		if(IS_GET){
			if(I('get.flag')==1){
				$model =D('User');
				$username = I('get.u');
				$password = I('get.p');
				var_dump($username,$password);
				$res = $model->regist($username,$password);
				if(!$res){
					echo $model->getError();
					die();
				}
				echo 'okk';
			}else{
				die("页面有错误，暂未解决");
				$this->display();
			}
		}elseif(IS_POST){
			dump(I('post.'));
			dumpDie($_SESSION);
			$username =I('post.username');
			$password =I('post.password');
			$checkcode =I('post.checkcode');
			//检查验证码是否正确
			$config = array(
			'length'	=> 4,
			'fontSize'  => 25,
			'codeSet'   => '12340',
			'fontttf'   =>  '6.ttf'
			);
			$obj = new \Think\Verify($config);
			if(!$obj->check($checkcode)){
				$this->ajaxReturn(array('status'=>0,'msg'=>'验证码错误'));
			}
			//实例化模型对象 调用方法入库
			$model =D('User');
			$res = $model->regist($username,$password);
			if(!$res){
				$this->ajaxReturn(array('status'=>0,'msg'=>$model->getError()));
			}
			$this->ajaxReturn(array('status'=>1,'msg'=>'ok'));
		}
	}
	
	public function code(){
		$config = array(
			'length'	=> 4,
			'fontSize'  => 25,
			'codeSet'   => '12340',
			'fontttf'   =>  '6.ttf'
		);
		$obj = new \Think\Verify($config);
		$obj->entry();
	}
	
	public function login(){
		echo '<h1>Before Login:</h1>';
		echo 'session:';
		dump(I('session.'));
		echo 'cookie:';
		dump(I('cookie.'));
		//前端代码错误，故暂时用简单代码替代
		$model =D('User');
		$username = I('get.u');
		$password = I('get.p');
		var_dump($username,$password);
		$res = $model->login($username,$password);
		if(!$res){
			echo $model->getError();
			die();
		}
		echo 'Login Success';
		dump(I('session.'));
		echo 'Current cookie:';
		dump(I('cookie.'));
		
	}
	
	public function logout(){
		echo 'Before emptying:';
		dump(I('session.'));
		session('user',null);
		session('user_id',null);
		// $this->redirect('/');
		echo 'After emptying:';
		dump(I('session.'));
	}
}







