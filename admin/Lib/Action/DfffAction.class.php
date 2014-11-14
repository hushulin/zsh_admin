<?php 
/**
* 系统配置
*/
class DfffAction extends CommonAction
{
	public function index()
	{
		$this->display();
	}

	public function baseinfo()
	{
		$this->display();
	}

	public function authff()
	{
		$this->display();
	}

	public function payinfo()
	{
		$this->display();
	}

	public function interfaceff()
	{
		$this->display();
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

}

 ?>