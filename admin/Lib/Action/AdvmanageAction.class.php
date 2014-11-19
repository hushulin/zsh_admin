<?php
// +----------------------------------------------------------------------
// | 掌生活
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.jiepool.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Eric(1023753031@qq.com)
// +----------------------------------------------------------------------


class AdvmanageAction extends CommonAction
{
	
	public function index()
	{
		redirect(U('advpos'));
	}

	public function advpos()
	{
		$model = D('adv');
		$list = $this->_filter($model);
		foreach ($list as $key => $value) {
			$list[$key]['adv_id'] = D('advposition')->where("`id` = {$value['adv_id']}")->getField('name');
		}
		$this->assign('list',$list);
		$this->display();
	}

	// 删除广告
	public function deladvpos()
	{
		$model = D('adv');
		$result = $model->delete($_GET['id']*1);
		if ($result) {
			$this->success('删除广告成功');
		}else {
			$this->error('删除广告发生错误');
		}
	}

	// 启用广告
	public function changeadvpos()
	{
		$model = D('adv');
		$id = $_GET['id']*1;
		$result = $model->where("`id` = {$id}")->setField('is_effect',1);
		if ($result) {
			$this->success('启用广告成功');
		}else {
			$this->error('启用广告发生错误或者已经启用');
		}
	}

	public function flash()
	{
		$this->display();
	}

	// 广告位管理
	public function advposition()
	{
		$model = D('advposition');
		$this->_list($model);
		$this->display();
	}

	// 添加广告位
	public function addadvposition()
	{
		if ($_POST) {
			$model = D('advposition');

			
			$_POST['create_time'] = time();
			$model->create();
			$result = $model->add();
			if ($result) {
				$this->success('添加广告位成功');
			}else {
				$this->error($model->getError());
			}
		} else {
			$this->display();
		}
	}

	// 删除广告位
	public function deladvposition()
	{
		$model = D('advposition');
		$result = $model->delete($_GET['id']*1);
		if ($result) {
			$this->success('删除广告位成功');
		}else {
			$this->error('删除广告位发生错误');
		}
	}

	// 添加广告 
	public function advaddto()
	{
		if ($_POST) {
			$model = D('adv');
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

			$model->create();
			$result = $model->add();
			if ($result) {
				$this->success('添加广告成功');
			}else {
				$this->error('添加广告失败');
			}
		} else {
			$adv_position = D('advposition')->where("`status` = 1")->select();
			$this->assign('adv_position',$adv_position);
			$this->display('addadvpos');
		}
	}

}
?>