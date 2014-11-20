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
		$this->display();
	}
}
 ?>