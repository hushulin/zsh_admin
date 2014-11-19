<?php
// +----------------------------------------------------------------------
// | 掌生活
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.jiepool.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Eric(1023753031@qq.com)
// +----------------------------------------------------------------------


class ContentmanageAction extends CommonAction
{
	
	public function index()
	{
		redirect(U('onicon'));
	}

	public function category()
	{
		$this->display();
	}

	// 开机画面
	public function onicon()
	{
		$model = D('onicon');
		$this->_list($model);
		$this->display();
	}

	// 添加开机画面
	public function addonicon()
	{
		if ($_POST) {
			$model = D('onicon');

			$file_upload_flag = false;
			foreach($_FILES as $f){
				if($f['size']>0){
					$file_upload_flag = true;
					break;
				}
			}

			$img = '';
			if($file_upload_flag){
				$arr_img = $this->uploadImage();
				if($arr_img['status']==0)
				{
					$this->error($arr_img['info']);
				}
				$img = 'http://'.$_SERVER['HTTP_HOST'].$arr_img['data'][0]['recpath'].$arr_img['data'][0]['savename'];
			}
			$_POST['img'] = $img;

			$_POST['area_id'] = $_POST['pid'];

			$_POST['create_time'] = time();
			$model->create();
			$result = $model->add();
			if ($result) {
				$this->success('添加画面成功');
			}else {
				$this->error($model->getError());
			}
		} else {
			$prov = D('AreaNew')->where("`pid` = 0")->select();
			$this->assign('prov',$prov);
			$this->display();
		}
	}

	// 删除开机画面
	public function delonicon()
	{
		$model = D('onicon');
		$result = $model->delete($_GET['id']*1);
		if ($result) {
			$this->success('删除成功');
		}else {
			$this->error('删除发生错误');
		}
	}

	public function getCity()
	{
		$prov_id = $_GET['prov_id']*1;
		$list = D('AreaNew')->where("`pid` = {$prov_id}")->select();
		echo json_encode($list,true);
	}

	public function getArea()
	{
		$city_id = $_GET['city_id']*1;
		$list = D('AreaNew')->where("`pid` = {$city_id}")->select();
		echo json_encode($list,true);
	}

	// 内容审核
	public function check()
	{
		$model = D('persongood');
		if ($_GET['status'] == 1) {
			$map['status'] = 1;
		}
		$list = $this->_filter($model,$map);
		foreach ($list as $key => $value) {
			$list[$key]['shop_name'] = D('personalshop')->where("`id` = {$value['shop_id']}")->getField('name');
			$list[$key]['good_photo'] = D('Picture')->where("`id` = {$value['good_photo']}")->getField('path');
		}
		$this->assign('list',$list);
		$this->display();
	}

	// 通过内容
	public function checkpersongood()
	{
		$model = D('persongood');
		$id = $_GET['id']*1;
		$result = $model->where("`id` = {$id}")->setField('status',1);
		if ($result) {
			$this->success('通过成功');
		}else {
			$this->error($model->getError());
		}
	}

	// 屏蔽内容
	public function delpersongood()
	{
		$model = D('persongood');
		$result = $model->delete($_GET['id']*1);
		if ($result) {
			$this->success('屏蔽成功');
		}else {
			$this->error('屏蔽发生错误');
		}
	}

	// 添加屏蔽关键字库
	public function addkws()
	{
		$aid = $this->aid;
		$model = D('kws');
		if ($_POST) {
			if ($model->find($aid)) {
				$model->create();
				$result = $model->save();
				$msg = '屏蔽关键字库保存成功';
			}else {
				$model->create();
				$result = $model->add();
				$msg = '屏蔽关键字库新增成功';
			}
			if ($result) {
				$this->success($msg);
			}else {
				$this->error($model->getError());
			}
		}else {
			
			$text = $model->where("`id` = {$aid}")->getField('text');
			$this->assign('text',$text);
			$this->assign('aid',$aid);
			$this->display();
		}
	}

	// 发布内容
	public function publish()
	{
		if ($_POST) {
			$model = D('persongood');

			$file_upload_flag = false;
			foreach($_FILES as $f){
				if($f['size']>0){
					$file_upload_flag = true;
					break;
				}
			}

			$img = '';
			if($file_upload_flag){
				$arr_img = $this->uploadImage();
				if($arr_img['status']==0)
				{
					$this->error($arr_img['info']);
				}
				$img = 'http://'.$_SERVER['HTTP_HOST'].$arr_img['data'][0]['recpath'].$arr_img['data'][0]['savename'];
			}
			$imgs['path'] = $img;
			$imgs['status'] = 1;
			$imgs['create_time'] = time();
			$re_img = D('Picture')->add($imgs);
			$_POST['good_photo'] = $re_img;

			$_POST['create_time'] = time();
			$_POST['status'] = 1;
			$model->create();
			$result = $model->add();
			if ($result) {
				$this->success('添加内容成功');
			}else {
				$this->error($model->getError());
			}
		} else {
			// 店铺
			$shop_list = D('personalshop')->select();
			$this->assign('shop_list',$shop_list);
			// 
			$this->display();
		}
		
	}

}
?>