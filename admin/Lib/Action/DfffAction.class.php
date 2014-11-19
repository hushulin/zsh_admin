<?php 
/**
* 系统配置
*/
class DfffAction extends CommonAction
{
	public function index()
	{
		redirect(U('adminmanage'));
	}

	public function baseinfo()
	{
		$this->display();
	}

	// 权限安全-
	public function authff()
	{
		redirect(U('addapi'));
	}

	public function payinfo()
	{
		$this->display();
	}

	public function interfaceff()
	{
		$model = D('interfaceff');
		$this->_list($model,$map,'id',true);
		$this->display();
	}

	// 添加接口
	public function addapi()
	{
		if ($_POST) {
			$model = D('interfaceff');
			$model->create();
			$result = $model->add();
			if ($result) {
				$this->success('添加接口成功');
			}else {
				$this->error('添加接口失败');
			}
		} else {
			$this->display();
		}
	}

	// 删除该接口
	public function delapi()
	{
		$model = D('interfaceff');
		$result = $model->delete($_GET['id']*1);
		if ($result) {
			$this->success('删除成功');
		}else {
			$this->error('删除发生错误');
		}
	}

	// APP列表 (服务到家)
	public function appff()
	{
		$model = D('AppsList');

		$list = $model->select();
		$this->assign('list',$list);
		$this->_list($model);
		$this->display();
	}

	// 新增应用
	public function add_appff()
	{
		$model = D('AppsList');
		if ($_POST) {
			$face = $this->uploadImage();
			if ($model->create()) {
				if ($face['status'] == 1) {
						$model->app_icon = 'http://'.$_SERVER['HTTP_HOST'].$face['data'][0]['recpath'].$face['data'][0]['savename'];
					}
				
					$re = $model->add();
					if ($re) {
						$this->success('创建成功');
					}else {
						$this->error('创建失败');
					}
				}else {
					$this->error('创建失败');
				}
		}else {
			$this->display();
		}
	}

	// 删除应用
	public function del_appff()
	{
		$id = $_GET['id'];
		if (D('AppsList')->delete($id)) {
			$this->success('删除成功');
		}else {
			$this->error('删除失败');
		}
	}

	// 应用子分类
	public function child_appff()
	{
		$app_id = $_GET['id'];
		$model = D('TypeList');
		$list = $model->where("`app_id` = {$app_id}")->select();
		$this->assign('list',$list);
		$this->display();
	}

	// 删除应用子分类
	public function del_child_appff()
	{
		$id = $_GET['id'];
		if (D('TypeList')->delete($id)) {
			$this->success('删除成功');
		}else {
			$this->error('删除失败');
		}
	}

	// 新增应用子分类
	public function add_child_appff()
	{
		$app_id = $_GET['id'];
		$model = D('TypeList');
		if ($_POST) {
			if ($model->create()) {
					$model->app_id = $app_id;
					$re = $model->add();
					if ($re) {
						$this->success('创建成功');
					}else {
						$this->error('创建失败');
					}
				}else {
					$this->error('创建失败');
				}
		}else {
			$this->display();
		}
	}

	// 管理员账号管理-列表
	public function adminmanage()
	{
		$model = D('Admin');
		$map['role_id'] = 4;
		$map['is_effect'] = 1;
		$this->_list($model,$map,'id');
		$this->display();
	}

	// 添加管理员账号
	public function addadmin()
	{
		if ($_POST) {
			$model = D('Admin');
			$data['role_id'] = 4;
			$data['is_effect'] = 1;
			$data['adm_name'] = $_POST['adm_name'];
			$data['adm_password'] = md5($_POST['adm_password']);
			$data['phone'] = $_POST['phone'];
			if ($_POST['adm_password'] != $_POST['adm_password2']) {
				$this->error('两次输入不一致');
			}
			if (strlen($_POST['adm_password']) < 6) {
				$this->error('密码不能少于6个字符');
			}
			$result = $model->add($data);
			if ($result) {
				$this->success('添加成功');
			} else {
				$this->error('添加失败');
			}
			
		} else {
			$this->display();
		}
		
	}

	// 删除管理员
	public function deladmin()
	{
		$model = D('Admin');
		$result = $model->delete($_GET['id']*1);
		if ($result) {
			$this->success('删除成功');
		}else {
			$this->error('删除发生错误');
		}
	}

	// 积分行为
	public function scorebehavior()
	{
		$model = D('scorebehavior');
		$this->_list($model);
		$this->display();
	}

	// 添加积分行为
	public function addscorebehavior()
	{
		if ($_POST) {
			$model = D('scorebehavior');
			foreach ($_POST as $key => $value) {
				$data[$key] = is_string($value)?$value:$value*1;
			}
			$data['create_time'] = time();
			$result = $model->add($data);
			if ($result) {
				$this->success('添加积分行为成功');
			}else {
				$this->error($model->getError());
			}
		} else {
			$this->display();
		}
	}

	// 删除该积分行为
	public function delbehavior()
	{
		$model = D('scorebehavior');
		$result = $model->delete($_GET['id']*1);
		if ($result) {
			$this->success('删除成功');
		}else {
			$this->error('删除发生错误');
		}
	}

	// 配置APP模块
	public function appall()
	{
		$model = D('appall');
		$this->_list($model);
		$this->display();
	}

	// 添加系统服务
	public function addappall()
	{
		if ($_POST) {
			$model = D('appall');
			foreach ($_POST as $key => $value) {
				$data[$key] = is_string($value)?$value:$value*1;
			}
			$data['create_time'] = time();
			$result = $model->add($data);
			if ($result) {
				$this->success('添加系统服务栏目成功');
			}else {
				$this->error($model->getError());
			}
		} else {
			$this->display();
		}
	}

	// 删除系统服务
	public function delappall()
	{
		$model = D('appall');
		$result = $model->delete($_GET['id']*1);
		if ($result) {
			$this->success('删除成功');
		}else {
			$this->error('删除发生错误');
		}
	}

}

 ?>