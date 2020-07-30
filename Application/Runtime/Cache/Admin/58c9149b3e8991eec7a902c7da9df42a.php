<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>ECSHOP 管理中心 - 商品回收站  </title>
<meta name="robots" content="noindex, nofollow">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="/Public/Admin/Styles/general.css" rel="stylesheet" type="text/css" />
<link href="/Public/Admin/Styles/main.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h1>
    <span class="action-span">
        <a href="__GROUP__/Goods/goodsList">商品列表</a>
    </span>
    <span class="action-span1"><a href="__GROUP__">ECSHOP 管理中心</a></span>
    <span id="search_id" class="action-span1"> - 商品回收站 </span>
    <div style="clear:both"></div>
</h1>
<div class="main-div">
    
<div class="form-div">
    <form action="" name="searchForm">
        <img src="/Public/Admin/Images/icon_search.gif" width="26" height="22" border="0" alt="search" />
        <!-- 关键字 -->
        关键字 <input type="text" name="keyword" size="15" />
        <input type="submit" value=" 搜索 " class="button" />
    </form>
</div>

<!-- 商品列表 -->
<form method="post" action="" name="listForm">
    <div class="list-div" id="listDiv">
        <table cellpadding="3" cellspacing="1">
            <tr>
                <th><input type="checkbox" />编号</th>
                <th>商品名称</th>
                <th>货号</th>
                <th>价格</th>
                <th>操作</th>
            </tr>
            <?php if(is_array($data["data"])): foreach($data["data"] as $key=>$val): ?><tr>
                <td><input type="checkbox" name="checkboxes[]" value="18" /><?php echo ($val["goods_id"]); ?></td>
                <td><?php echo ($val["goods_name"]); ?></td>
                <td><?php echo ($val["goods_sn"]); ?></td>
                <td align="right"><?php echo ($val["shop_price"]); ?></td>
                <td align="center"><a href="<?php echo U('recover','id='.$val['id']);?>">还原</a> | <a href="<?php echo U('remove','id='.$val['id']);?>">删除</a></td>
            </tr><?php endforeach; endif; ?>
            <!-- 分页 -->
            <tr>
                <td align="right" nowrap="true" colspan="6">
                    <div id="turn-page">
                        <?php echo ($data["pageStr"]); ?>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</form>

</div>
<div id="footer">
共执行 3 个查询，用时 0.162348 秒，Gzip 已禁用，内存占用 2.266 MB<br />
版权所有 &copy; 2005-2012 上海商派网络科技有限公司，并保留所有权利。</div>
</body>
</html>
<script type="text/javascript" src="/Public/Admin/Js/jquery-1.8.3.min.js"></script>