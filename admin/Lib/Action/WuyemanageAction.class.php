<?php
// +----------------------------------------------------------------------
// | 掌生活
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.jiepool.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Eric(1023753031@qq.com)
// +----------------------------------------------------------------------


class WuyemanageAction extends CommonAction
{
	
	public function index()
	{
		$this->display();
	}

	// 物业列表
	public function wuyelist()
	{
		$model = D('admin');
		$map['role_id'] = 5;
		$map['is_effect'] = 1;

		$this->_list($model,$map,'login_time');
		$this->display();
	}

	// 新增物业，跟新增商家不一样
	public function addwuye()
	{
		if ($_POST) {
			$model = D('admin');
			$model->create();
			$model->is_effect = 1;
			$model->adm_password = md5($_POST['adm_password']);
			$model->role_id = 5;
			$re = $model->add();
			if ($re) {
				$arr_img = $this->uploadImage();
				// var_dump($arr_img);
				$pic['path'] = 'http://'.$_SERVER['HTTP_HOST'].$arr_img['data'][0]['recpath'].$arr_img['data'][0]['savename'];
				$pic['status'] = 1;
				$pic['create_time'] = time();
				$res = D('picture')->add($pic);
				$da['id'] = $re;
				$da['name'] = $_POST['wuye_name'];
				$da['company'] = $_POST['wuye_company'];
				$da['create_time'] = time();
				$da['face'] = $res;
				D('wuye')->add($da);
				$this->success('新增成功');
			}else {
				$this->error('新增失败');
			}
		}else {
			$this->display();
		}
	}

}
?>