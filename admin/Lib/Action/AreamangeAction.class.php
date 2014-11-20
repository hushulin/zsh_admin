<?php 
/**
* 
*/
class AreamangeAction extends CommonAction
{
	// 
	public function index()
	{
		redirect(U('lists'));
	}

	public function lists()
	{
		$model = D('AreaNew');
		$map['wuye_id'] = array('neq',0);
		$this->_list($model,$map);
		$this->display();
	}

	// 添加小区
	public function add()
	{
		if ($_POST) {
			$model = D('AreaNew');
			// 处理图片
			$arr_img = $this->uploadImage();
			foreach ($arr_img['data'] as $key => $value) {
				$path = 'http://'.$_SERVER['HTTP_HOST'].$value['recpath'].$value['savename'];

				$_POST[$value['key']] = $path;
			}
			$model->create();
			$re = $model->add();
			if ($re) {
				$this->success('添加小区成功');
			}else {
				$this->error('添加小区失败');
			}
		}else {
			$prov = D('AreaNew')->where("`pid` = 0")->select();
			$this->assign('prov',$prov);
			// 物业
			$wuye_list = D('Wuye')->select();
			
			$this->assign('wuye_list',$wuye_list);
			$this->display();
		}
		
	}

	// 删除小区
	public function delredpaper()
	{
		$model = D('AreaNew');
		$result = $model->delete($_GET['id']*1);
		if ($result) {
			$this->success('删除成功');
		}else {
			$this->error('删除发生错误');
		}
	}

	// 配置小区栏目
	public function configcate()
	{
		if ($_POST) {
			$model = D('AreaNew');
			$id = $_POST['id'];
			$str = implode(',', $_POST['config']);
			$re = $model->where("`id` = {$id}")->setField('appfields',$str);
			if ($re) {
				$this->success('配置小区栏目成功');
			}else {
				$this->error('配置小区栏目失败');
			}
		}else {
			// 物业
			$model = D('AreaNew');
			$map['wuye_id'] = array('neq',0);
			$list = $model->where($map)->select();
			$this->assign('list',$list);
			$this->display();
		}
	}
}
 ?>