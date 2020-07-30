<?php
namespace Admin\Controller;

class LogoutController extends CommonController{
	public function logout(){
		cookie('admin',null);
		dump($_COOKIE);
	}
}