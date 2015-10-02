<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends MY_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index(){
		$this->model();//可以将放在控制中的构造函数下，不过不建议这么做
		//添加：
			$flag=$this->model->update_field('test', array('username'=>'youyiyiper'),'num','+1');
			var_dump($flag);

		//删除：
			$flag=$this->model->delete_data('test', array('username'=>'youyiyiper'));
			var_dump($flag);

		//修改
			$flag=$this->model->insert_data('test', array('username'=>'youyiyiper','password'=>'xxx'));
			var_dump($flag);

		//查询
			$param = array('table'=>'users','field'=>'id,username','orderby'=>'id desc');
			$flag=$this->model->get_list2($param);
			var_dump($flag);


		$this->load->view('welcome_message');
	}
}
