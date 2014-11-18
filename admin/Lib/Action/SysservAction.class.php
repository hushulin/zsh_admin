<?php
// +----------------------------------------------------------------------
// | 掌生活
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.jiepool.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Eric(1023753031@qq.com)
// +----------------------------------------------------------------------


class SysservAction extends CommonAction
{
	
	public function index()
	{
		redirect(U('redpaper'));
	}

	public function score()
	{
		$this->display();
	}

	public function redpaper()
	{
		$model = D('redpaper');
		$this->_list($model);
		$this->display();
	}

	// 删除红包
	public function delredpaper()
	{
		$model = D('redpaper');
		$result = $model->delete($_GET['id']*1);
		if ($result) {
			$this->success('删除成功');
		}else {
			$this->error('删除发生错误');
		}
	}

	//发红包
	public function pushredpaper()
	{
		$model = D('redpaper');
		$where['id'] = $_GET['id']*1;
		$result = $model->where($where)->setField('status','1');
		if ($result) {
			$this->success('发放成功');
		}else {
			$this->error('已发放过，不需重复发放');
		}
	}

	// 添加红包
	public function addredpaper()
	{
		if ($_POST) {
			$model = D('redpaper');
			$begin_time = strtotime($_POST['begin_time']);
			$end_time = strtotime($_POST['end_time']);
			$_POST['begin_time'] = $begin_time;
			$_POST['end_time'] = $end_time;
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

			$_POST['status'] = 0;
			$_POST['create_time'] = time();
			$model->create();
			$result = $model->add();
			if ($result) {
				$this->success('添加红包成功');
			}else {
				$this->error('添加红包失败');
			}
		} else {
			$this->display();
		}
	}

	// 信息推送
	public function pushs()
	{
		$model = D('imtemplate');
		$map['eid'] = $this->aid;
		$this->_list($model,$map);
		$this->display();
	}

	// 信息模板选择
	public function selct()
	{
		$id = $_GET['id'];
		$eid = $this->aid;
		$model = D('imtemplate');
		$model->where("`eid` = {$eid}")->setField('is_effect',0);
		$model->where("`id` = {$id}")->setField('is_effect',1);
		die('设置成功');
	}

	// 新增信息模板
	public function addTmpl()
	{
		$eid = $this->aid;
		if ($_POST) {
			$model = D('imtemplate');
			$_POST['eid'] = $eid;
			$model->create();
			$re = $model->add();
			if ($re) {
				$this->success('新增短信模板成功');
			}else {
				$this->error('新增短信模板失败');
			}
		}else {
			$this->display();
		}
	}

	// 信息推送
	public function mqs()
	{
		$eid = $this->aid;
		if ($_POST) {
			$smstmpl = D('imtemplate')->where("`is_effect` = 1 and `eid` = {$eid}")->find();
			if ($smstmpl) {
				$users = $_POST['users'];
				if (count($users) > 0) {
					$smstmpl_title = $smstmpl['title'];
					$smstmpl_content = $smstmpl['content'];
					$model = D('imsms');
					foreach ($users as $key => $value) {
						$data['title'] = $smstmpl_title;
						$data['content'] = $smstmpl_content;
						$data['code'] = $_POST['code'];
						$data['type'] = 1;
						$data['create_time'] = time();
						$data['from_name'] = D('Admin')->where("`id` = {$eid}")->getField('adm_name');
						$data['from_id'] = $eid;
						$data['to_name'] = D('User')->where("`id` = {$key}")->getField('user_name');
						$data['to_id'] = $key;
						$model->add($data);
					}
					$this->success('推送成功');
				}else {
					$this->error('未选择要推送的用户');
				}
			}else {
				$this->error('未选择模板，请先选择模板');
			}
		}else {
			// 获取可推送用户
			$users = D('User')->where(1)->select();
			$this->assign('users',$users);
			$this->display();
		}
	}

	public function census()
	{
		$map['from_id'] = $this->aid;
		$model = D('imsms');
		$this->_list($model,$map);
		$this->display();
	}

	// 处理反馈
	public function handlefeed()
	{
		redirect(U('census'));
	}

	public function handlecompen()
	{
		redirect(U('Businessmanage/compensation'));
	}

}
?>