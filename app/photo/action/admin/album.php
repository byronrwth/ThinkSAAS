<?php 
defined('IN_TS') or die('Access Denied.');
switch($ts){
	case "list":
		
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		
		$lstart = $page*10-10;
		
		$url = SITE_URL.'index.php?app=photo&ac=admin&mg=album&ts=list&page=';
		
		$arrAlbum = $db->findAll("select * from ".dbprefix."photo_album order by addtime desc limit $lstart,10");
		
		$albumNum = $db->find("select count(*) from ".dbprefix."photo_album");
		
		$pageUrl = pagination($albumNum['count(*)'], 10, $page, $url);
		
		include template("admin/album_list");
		break;
	
	//图片 
	case "photo":
		$albumid = $_GET['albumid'];
		
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		
		$lstart = $page*10-10;
		
		$url = SITE_URL.'index.php?app=photo&ac=admin&mg=album&ts=photo&albumid='.$albumid.'&page=';
		
		$arrPhoto = $db->findAll("select * from ".dbprefix."photo where albumid='$albumid' limit $lstart,10");
		
		$photo_num = $db->find("select count(photoid) from ".dbprefix."photo where albumid='$albumid'");
		
		$pageUrl = pagination($photo_num['count(photoid)'], 10, $page, $url);
		
		include template("admin/album_photo");
		
		break;
		
	//删除相册
	case "del_album":
		$albumid = $_GET['albumid'];
		
		$db->query("delete from ".dbprefix."photo_album where albumid='$albumid'");
		$arrPhoto = $db->findAll("select * from ".dbprefix."photo where albumid='$albumid'");
		foreach($arrPhoto as $item){
			unlink('uploadfile/photo/'.$item['photourl']);
		}
		
		$db->query("delete from ".dbprefix."photo where albumid='$albumid'");
		
		qiMsg("相册删除成功！");
		
		break;
		
	//删除照片
	case "del_photo":
		$photoid = $_GET['photoid'];
		
		$strPhoto = $db->find("select * from ".dbprefix."photo where photoid='$photoid'");
		$albumid = $strPhoto['albumid'];
		
		$db->query("delete from ".dbprefix."photo where photoid='$photoid'");
		
		unlink('uploadfile/photo/'.$strPhoto['photourl']);
		
		$count_photo = $db->findCount("select * from ".dbprefix."photo where albumid='$albumid'");
		
		$db->update('photo_album',array(
				'count_photo'=>$count_photo,
			),array(
				'albumid'=>$albumid,
			));
		
		qiMsg("图片删除成功!");
		
		break;
		
	//设为封面
	case "face":
		$photoid = $_GET['photoid'];
		$strPhoto = $db->find("select * from ".dbprefix."photo where photoid='$photoid'");
		
		$albumid = $strPhoto['albumid'];
		$albumface = $strPhoto['photourl'];
		
		$db->update('photo_album',array(
			'albumface'=>$albumface,
		),array(
			'albumid'=>$albumid,
		));
		
		qiMsg("封面设置成功！");
		
		break;
		
	//统计 
	case "count":
		
		$arrAlbum = $db->findAll("select albumid from ".dbprefix."photo_album");
		
		foreach($arrAlbum as $item){
			$albumid = $item['albumid'];
			$count_photo = $db->findCount("select photoid from ".dbprefix."photo where albumid='$albumid'");

			$db->update('photo_album',array(
				'count_photo'=>$count_photo,
			),array(
				'albumid'=>$albumid,
			));
		}
		
		qiMsg("统计完成！");
		
		break;
		
	//推荐相册 
	case "isrecommend":
	
		$albumid = $_GET['albumid'];
		
		$strAlbum = $db->find("select isrecommend from ".dbprefix."photo_album where `albumid`='$albumid'");
		
		if($strAlbum['isrecommend']==0){
			
			$db->update('photo_album',array(
				'isrecommend'=>1,
			),array(
				'albumid'=>$albumid,
			));
			
		}else{

			$db->update('photo_album',array(
				'isrecommend'=>0,
			),array(
				'albumid'=>$albumid,
			));
			
		}
	
		qiMsg("操作成功！");
	
		break;
	
}