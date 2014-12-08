<?php
// +----------------------------------------------------------------------
// | 掌生活
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.jiepool.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Eric(1023753031@qq.com)
// +----------------------------------------------------------------------


class BusinessmanageAction extends CommonAction
{
	
	public function index()
	{
		$this->display();
	}

	public function typeconf()
	{
		$this->display();
	}

	public function typemanage()
	{
		$this->display();
	}

	// 商家信息
	public function info()
	{
		$model = D('admin');
		$map['role_id'] = 6;
		$map['is_effect'] = 1;

		$this->_list($model,$map,'login_time');
		$this->display();
	}

	// 商品信息
	public function goodsinfo()
	{
		$model = D('Deal');
		$map = array();
		$this->_list($model,$map);
		$this->display();
	}

	// 删除商品
	public function delgood()
	{
		$id = $_GET['id'];
		D('Deal')->delete($id);
		$this->success('删除成功');
	}

	// 下架
	public function set0()
	{
		$id = $_GET['id'];
		D('Deal')->where("`id` = {$id}")->setField('is_effect',0);
		$this->success('下架成功');
	}

	// 订单
	public function ordermanage()
	{
		$model = D('DealOrder');
		$this->_list($model);
		$this->display();
	}

	// 删除订单
	public function delorder()
	{
		$id = $_GET['id'];
		D('DealOrder')->delete($id);
		$where['order_id'] = $id;
		D('DealOrderItem')->where($where)->delete();
		D('DealOrderLog')->where($where)->delete();
		$this->success('删除成功');
	}

	// 取消订单
	public function set3order()
	{
		$id = $_GET['id'];
		D('DealOrder')->where("`id` = {$id}")->setField('order_status',3);
		$this->success('取消成功');
	}

	// 指定商家分类
	public function addType()
	{
		if ($_POST) {
			$model = D('ServApps');
			$where['rel_model'] = $_POST['rel_model'];
			$where['rel_model_id'] = $_POST['rel_model_id'];
			// $where['app_id'] = $_POST['app_id'];
			// $where['type_id'] = $_POST['type_id'];
			$origin = $model->where($where)->find();
			
			if ($origin) {
				$_POST['id'] = $origin['id'];
				$model->create();
				$result = $model->save();
				if ($result) {
					$this->success('指定成功');
				}else {
					$this->error('指定失败');
				}
			}else {
				$model->create();
				$result = $model->add();
				if ($result) {
					$this->success('指定成功');
				}else {
					$this->error('指定失败');
				}
			}
		}else {
			// 获取功能列表
			$app_list = D('AppsList')->select();
			$this->assign('alist',$app_list);
			$this->display();
		}
	}

	// AJAX 获取指定功能ID的分类列表
	public function getTypes()
	{
		$app_id = $_GET['app_id'];
		$where['app_id'] = $app_id;
		$list = D('TypeList')->where($where)->select();
		die(json_encode($list,true));
	}

	// 新增商家登录账号并授权
	public function addBusiness()
	{
		if ($_POST) {
			$model = D('admin');
			$model->create();
			$model->is_effect = 1;
			$model->adm_password = md5($_POST['adm_password']);
			$model->role_id = 6;
			$re = $model->add();
			if ($re) {
				$this->success('新增成功');
			}else {
				$this->error('新增失败');
			}
		}else {
			$this->display();
		}
	}

	// 查看该商家下的店铺
	public function seelocation()
	{
		// 账户ID
		$id = $_GET['id'];
		$where['account_id'] = $id;
		$location_id = D('supplier_account_location_link')->where($where)->select();
		if ($location_id) {
			foreach ($location_id as $key => $value) {
				 $arr_location_id[] = $value['location_id'];
			}
			$model = D('supplier_location');
			$map['id'] = array('IN',$arr_location_id);
			$this->_list($model,$map,'id');
		}
		$this->display();
	}

	// 新增店铺(重要)
	public function addlocation()
	{
		// 账户ID
		$bid = $_GET['bid'];

		// 城市列表
		$city = D('AreaNew');
		$city_list = $city->order("`sort` DESC")->select();
		$city_tree = list_to_tree($city_list,$pk='id',$pid='pid',$child='_child',$root=0);

		$city_tree2 = tree_to_list2($city_tree);

		if ($_POST) {
			$face = $this->uploadImage();
			
			$biz = D('supplier_location');
			$biz->create();
			if ($face['status'] == 1) {
				$biz->preview = 'http://'.$_SERVER['HTTP_HOST'].$face['data'][0]['recpath'].$face['data'][0]['savename'];
			}
			$biz->route = 0;
			$biz->is_effect = 1;
			$re = $biz->add();
			if ($re) {
				$ecs['account_id'] = $bid;
				$ecs['location_id'] = $re;
				D('supplier_account_location_link')->add($ecs);
				$this->success('商户信息保存成功');
			}else {
				$this->error('商户信息保存失败');
			}
		}else {
			$this->assign('business_account',$name);
			$this->assign('business_info',$business[0]);
			$this->assign('citys',$city_tree2);
			$this->display();
		}
	}

	public function integrity()
	{
		$this->display();
	}

	// 先赔
	public function compensation()
	{
		$model = D('compensate');
		// $map = '';
		$this->_list($model);
		$this->display();
	}

	// 删除无效的先赔或者已经处理完的先赔
	public function del_compensation()
	{
		$id = $_GET['id'];
		$model = D('compensate');
		$model->delete($id);
		$model_log = D('compenprocess');
		$model_log->where("`compen_id` = {$id}")->delete();
		$this->success('删除成功');
	}

	// 后台先赔
	public function do_compensation()
	{
		$id = $_GET['id'];
		$account_id = $_GET['account_id'];
		$total_price = $_GET['total_price'];
		$user_id = $_GET['user_id'];
		// 商家ID与诚信金
		$location_id = D('supplier_account_location_link')->where("`account_id` = {$account_id}")->getField('location_id');
		$chengxinjin = D('supplier_location')->where("`id` = {$location_id}")->getField('event_count');
		// 判断是否已处理
		if (D('compensate')->where("`id` = {$id}")->getField('status') == 0) {
			$this->error('该先赔已经处理');
		}
		// 判断保证金余额
		if ($chengxinjin >= $total_price) {
			$cur = $chengxinjin-$total_price;
			D('supplier_location')->where("`id` = {$location_id}")->setField('event_count',$cur);
			D('User')->query("UPDATE ".C('DB_PREFIX')."user SET `money` = `money`+{$total_price} WHERE `id` = {$user_id}");
			// 写日志
			$data['compen_id'] = $id;
			$data['user_id'] = $user_id;
			$data['user_name'] = D('User')->where("`id` = {$user_id}")->getField('user_name');
			$data['create_time'] = time();
			$data['remarks'] = '商家ID为:'.$account_id.'的先赔已经处理,总价为:'.$total_price;
			$data['eb_name'] = D('admin')->where("`id` = {$account_id}")->getField('adm_name');
			D('compenprocess')->add($data);
			// 改状态
			D('compensate')->where("`id` = {$id}")->setField("status",0);
			$this->success('先赔成功');
		}else {
			$this->error('诚信保证金余额不足');
		}

	}

}
?>