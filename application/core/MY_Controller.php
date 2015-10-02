<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller{

    //定义模型对象
    protected $model=null;

    public function __construct(){
        parent::__construct();
        //不建议放在这里,效率高低影响吧
        //$this->model = load_class('Model', 'core');
    }

    //初始化模型对象
    public function model(){
        $this->model = load_class('Model', 'core');
    }
}
