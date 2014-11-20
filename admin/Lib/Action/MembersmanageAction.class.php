<?php 
/**
* 
*/
class MembersmanageAction extends CommonAction
{
	// 用户列表跳转
	public function index()
	{
		redirect(U('adminlist'));
	}

	// 用户列表 
	public function adminlist()
	{
		$model = D('User');
		$list = $this->_filter($model);
		foreach ($list as $key => $value) {
			$list[$key]['img'] = D('Picture')->where("`id` = {$value['face_id']}")->getField('path');
		}
		$this->assign('list',$list);
		$this->display();
	}

	// 删除用户
	public function deluser()
	{
		$model = D('user');
		$result = $model->delete($_GET['id']*1);
		if ($result) {
			$this->success('删除成功');
		}else {
			$this->error('删除发生错误');
		}
	}

	// 认证用户
	public function changeuser()
	{
		$model = D('user');
		$id = $_GET['id']*1;
		$result = $model->where("`id` = {$id}")->setField('verify',1);
		if ($result) {
			$this->success('认证成功');
		}else {
			$this->error('已认证');
		}
	}

	// 添加用户
	public function addadmin()
	{
		if ($_POST) {
			$model = D('User');
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
			$res = D('Picture')->add($imgs);
			$_POST['face_id'] = $res;

			$_POST['user_pwd'] = md5(123456);
			$_POST['group_id'] = 1;
			$_POST['level_id'] = 1;
			$_POST['create_time'] = time();

			if (D('User')->where("`mobile` = {$_POST['mobile']}")->find()) {
				$this->error('该手机号已存在');
			}

			$model->create();
			$result = $model->add();
			if ($result) {
				$this->success('添加用户成功');
			}else {
				$this->error('添加用户失败');
			}
		} else {
			$prov = D('AreaNew')->where("`pid` = 0")->select();
   			$this->assign('prov',$prov);
			$this->display();
		}
		
	}

	// 统计
	public function censuschart()
	{
		$this->display();
	}

	// 统计登录
	public function getPoint()
	{
		$t = $_GET['t'];
		$t = substr($t, 0,10);
		$pre_t = $t-10;
		$next_t = $t+10;
		$count = D('User')->where("`login_time` > {$pre_t} and `login_time` < {$next_t}")->count();
		Log::write(D()->getLastSql());
		die($count);
	}
}