<?php 
/**
* 生成代码工具
*/
class GenAction extends Action
{
	public function index()
	{
		if ($_POST) {
			// $url = $_POST['url'];
			// $this->gen($url);
			$model = D('Menu');
			$list = $model->where("`tip` = 'admin'")->select();
			foreach ($list as $key => $value) {
				$this->gen($value['url']);
			}
		}else

		$this->display();
	}

	private function gen($url)
	{
		// 获得module_name 和 action_name
		$arr_url = parse_url($url);
		$get_url = $arr_url['query'];
		preg_match('/(?<=m=)\w+(?=&)/', $get_url,$match);
		preg_match('/(?<=a=)\w+(?=\b)/', $get_url,$match2);
		if (count($match) == 1) {
			$module_name = $match[0];
		}else {
			$count = count($match);
			$module_name = $match[$count-1];
		}
		if (count($match2) == 1) {
			$action_name = $match2[0];
		}else {
			$count = count($match2);
			$action_name = $match2[$count-1];
		}

		if (empty($module_name) || empty($action_name)) {
			return false;
		}

		// end 获得module_name 和 action_name
		
		// 创建控制器
		$action_dir = __DIR__;
		$extends = 'Action.class.php';
		$base_file_name = $module_name.$extends;
		$base_file = $action_dir.'/'.$base_file_name;
		if (file_exists($base_file)) {
			$contents  = file_get_contents($base_file);
			if (strstr($contents, 'public function '.$action_name)) {
					return false;
				}
			$func = <<< func

	public function $action_name()
	{
		\$this->display();
	}

}
?>
func;
			$base_file_handle = fopen($base_file, 'r+');
			if (!is_writable($base_file)) {
				return false;
			}else {
				fseek($base_file_handle, -5,SEEK_END);
				fwrite($base_file_handle, $func);
			}
			fclose($base_file_handle);
		}else {
			// 新建Action文件
			$new_file = fopen($base_file, 'w+');
			if (!is_writable($base_file)) {
				return false;
			}else {
				$cont = file_get_contents($action_dir.'/TAction.class.php');
				$cont = str_replace('@@T@@', $module_name, $cont);

				if ($action_name == 'index') {
					$func = <<< func
	public function $action_name()
	{
		\$this->display();
	}

}
?>
func;
				$cont = str_replace("}\n?>", $func, $cont);
				fwrite($new_file, $cont);
				}else {
					$func = <<< func
	public function $action_name()
	{
		\$this->display();
	}

}
?>
func;
				$cont = str_replace("}\n?>", $func, $cont);
				fwrite($new_file, $cont);
				}
			}
			fclose($new_file);
		}

		// 创建魔板
		$tmpl_dir = $action_dir.'/../../Tpl/admin/';
		$module_dir = $tmpl_dir.$module_name;
		if (!is_dir($module_dir)) {
			mkdir($module_dir,0777,true);
		}
		$tmpl = $module_dir.'/'.$action_name.'.html';
		if (!file_exists($tmpl)) {
			$html_handle = fopen($tmpl, 'w+');
			$content = file_get_contents($tmpl_dir.'t.html');
			fwrite($html_handle, $content);
			fclose($html_handle);
		}
		return true;
	}
}
 ?>