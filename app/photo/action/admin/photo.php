<?php 
defined('IN_TS') or die('Access Denied.');
switch($ts){
	
	case "list":
		
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		
		$lstart = $page*10-10;
		
		$url = SITE_URL.'index.php?app=photo&ac=admin&mg=photo&ts=list&page=';
		
		$arrPhoto = $db->findAll("select * from ".dbprefix."photo order by addtime desc limit $lstart,10");
		
		$photoNum = $db->find("select count(*) from ".dbprefix."photo");
		
		$pageUrl = pagination($photoNum['count(*)'], 10, $page, $url);
		
		include template("admin/photo_list");
		break;
		
	//推荐图片 
	case "isrecommend":
	
		$photoid = intval($_GET['photoid']);
		
		$strPhoto = $db->find("select isrecommend from ".dbprefix."photo where `photoid`='$photoid'");
		
		if($strPhoto['isrecommend']==0){

			$db->update('photo',array(
				'isrecommend'=>1,
			),array(
				'photoid'=>$photoid,
			));
			
		}else{

			$db->update('photo',array(
				'isrecommend'=>0,
			),array(
				'photoid'=>$photoid,
			));
		}
	
		qiMsg("操作成功！");
	
		break;
	
}