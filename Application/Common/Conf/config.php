<?php
return array(
	//'配置项'=>'配置值'
	'URL_MODEL'=>2,//设置URL模式为重写模式
	'DEFAULT_MODULE'=>'Home',//设置默认模块
	'TOKEN_ON'      =>    true,  // 是否开启令牌验证 默认关闭
	'TOKEN_NAME'    =>    '__hash__',    // 令牌验证的表单隐藏字段名称，默认为__hash__
	'TOKEN_TYPE'    =>    'md5',  //令牌哈希验证规则 默认为MD5
	'TOKEN_RESET'   =>    true,  //令牌验证出错后是否重置令牌 默认为true
	'view_filter' => array('Behavior\TokenBuild'),
	'URL_ROUTER_ON'         =>  true,   // 是否开启URL路由
    'URL_ROUTE_RULES'       =>  array(
		'sk/:name\d' => 'Admin/Common/test'
	), // 默认路由规则 针对模块
	//设置容许访问的模块
	'MODULE_ALLOW_LIST'=>array('Home','Admin'),
	//增加自定义的模板替换配置信息
	'TMPL_PARSE_STRING'=>array(
		'__PUBLIC_ADMIN__'=>'/Public/Admin',
		'__PUBLIC_HOME__' =>'/Public/Home'
	),

	/* 数据库设置 */
    'DB_TYPE'               =>  'mysql',     // 数据库类型
    'DB_HOST'               =>  '127.0.0.1', // 服务器地址
    'DB_NAME'               =>  'jxshop_official',          // 数据库名
    'DB_USER'               =>  'root',      // 用户名
    'DB_PWD'                =>  'root',          // 密码
    'DB_PORT'               =>  '3306',        // 端口
    'DB_PREFIX'             =>  'jx_',    // 数据库表前缀
	
	/* 跟踪信息 */
	'SHOW_PAGE_TRACE'		=> 'true',
);