<?php
/**
* 客户服务
*/
class ServicesAction extends CommonAction
{
	// 控制台
	public function index()
	{
		$this->display();
	}

	// 物业报修
	public function repairs()
	{
		$wuye_id = 1;
		$type = 2;
		$list = $this->getCRList($wuye_id,$type);
		$this->assign('list',$list);
		$this->display();
	}

	// 投诉处理
	public function complain()
	{
		$wuye_id = 1;
		$type = 1;
		$list = $this->getCRList($wuye_id,$type);
		$this->assign('list',$list);
		$this->display();
	}

	public function dealcr()
	{
		$cr_id = $_GET['id']*1;
		$crmodel = D('Cr');
		$where['id'] = $cr_id;
		$result = $crmodel->where($where)->setField('status',0);
		$this->success('处理成功');
	}
	
	// 获取投诉报修
	private function getCRList($wuye_id='', $type='')
	{
		if (empty($wuye_id) || empty($type)) {
			return false;
		}

		$crmodel = D('Cr');
		$usermodel = D('User');
		$userList = $usermodel->where("`wuye_id` = {$wuye_id}")->select();
		$userIds = array();
		foreach ($userList as $key => $value) {
			$userIds[] = $value['id'];
		}
		$where['user_id'] = array('IN',$userIds);
		$where['repair_type'] = $type;
		$LIST = $crmodel->where($where)->order('`pub_time` ASC')->select();
		foreach ($LIST as $key => $value) {
			$LIST[$key]['pub_time'] = date('Y年m月d日 H:i',$value['pub_time']);
			$LIST[$key]['status'] = $value['status'] == 1 ? '未处理' : '已处理';
		}
		return $LIST;
	}
}
 ?>