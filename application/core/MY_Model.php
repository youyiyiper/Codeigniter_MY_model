<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*@youyiyiper
model使用方式一：
    $this->load->model('test_model');
    test_model继承MY_model
    $this->test_model->xxx();自定义model扩展  做其他的特定操作

model使用方式二：
    直接调用MY_controller下的model初始化方法获取对象(如果需要的话)
    $this->model();//可以将放在控制中的构造函数下，不过不建议这么做
    $flag=$this->model->update_field('test', array('text'=>'xxx'),'num','+1');
    var_dump($flag);
*/

/**
 * 基础model
 */
class MY_Model extends CI_Model {

    /**
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * @youyiyiper
     * 增：一次插入单条或多条数据
     *
     * @param  	string 		$table 		表名名称
     * @return 	mixed     				成功返回最后插入的id值或影响的记录数,失败返回FALSE
     */
    public function insert_data($table='',$data=array()){
    	if(empty($table)){
    		return FALSE;
    	}

    	//不是数组或空数组
    	if(!is_array($data) || count($data) == 0){
    		return FALSE;
    	}

    	//一次插入一条数据
    	if(!isset($data[0])){
    		return $this->db->insert($table,$data) ? $this->db->insert_id() : FALSE;

    	//一次插入多条数据
    	}else if(is_array($data[0])){
    		return $this->db->insert_batch($table,$data) ? $this->db->affected_rows() : FALSE;
    	}
    }

    /**
     * @youyiyiper
     * 删：删除数据
     *
     * @param  	string 		$table 		表名名称
     * @param  	array 		$where 		删除条件,关联数组形式
     * @return  bool        			删除成功返回true,失败返回false
     */
    public function delete_data($table='',$where=array()){

    	if(empty($table) || count($where)==0){
    		return FALSE;
    	}

    	$this->db->delete($table,$where);

        return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
    }

    /**
     *@youyiyiper
     * 改：修改数据
     *
     * @param  	string 		$table 		表名名称
     * @param   mixed       $where      修改条件,可以是字符串形式或关联数组形式
     * @param  	array 		$data 		修改的数据
     * @param  	string 		$field 		修改的字段/修改多条记录的时候才使用
     * @return  bool        			修改成功返回true,失败返回false
     */
    public function update_data($table='',$where='',$data=array(),$field=''){
    	if(empty($table) || count($data)==0){
    		return FALSE;
    	}

    	//一次修改一条数据
    	if(!isset($data[0])){
    		$this->db->where($where)->update($table,$data);

    	//一次修改多条数据
    	}else if(is_array($data[0]) && $field!==''){
    		$this->db->update_batch($table,$data,$field);
    	}

    	return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
    }

    /**
     * @youyiyiper
     * 改：修改特定的字段加n或减n
     *
     * @param  	string 		$table 		表名名称
     * @param  	mixed 		$where 		修改条件,可以是字符串形式或关联数组形式
     * @param  	array 		$field 		修改的字段
     * @param  	string 		$auto 		自增或自减+n / -n
     * @return  bool        			修改成功返回true,失败返回false
     */
    public function update_field($table='',$where='',$field='',$auto=''){

    	if(empty($table) || $where=='' || $field=='' || $auto==''){
    		return FALSE;
    	}

    	$this->db->where($where)->set($field, $field.$auto, FALSE)->update($table);

		return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
    }

    /**
	 *@youyiyiper
     * 查：返回多条数据方式一(该方法传入的参数较多，使用时比较别扭)
     *
     * @param   string      $table      表名名称
     * @param   string      $where      查询条件，可数组可字符串
     * @param   string      $field      查询字段，默认查询所有 *
     * @param   string      $orderby    排序
     * @param   number      $offset     偏移量，默认为0
     * @param   number      $limit      每页显示条数，默认为10
     * @param   array       $join       关联关系，二维数组，可查看下面 join($join) 方法
     * @param   array       $like       模糊查询  二维数组，可查看下面 like($like) 方法
     * @param   string      $groupby    分组
     * @param   string      $having     过滤
     * @return  mixed                   返回多条数据，为空返回空数组；操作错误则返回false
     */
    public function get_list($table = '', $where = '', $field = "*", $orderby = '',$offset =0, $limit = 10, $join = array(), $like=array(), $groupby = '', $having = ''){
        
        if(empty($table)){
          return FALSE;
        }

        if(empty($field)){
           $field="*";
        }

        //查询字段
        $this->db->select($field);

        //条件
        if(!empty($where)){
            $this->db->where($where);
        }

        //排序    
        if(!empty($orderby)){
            $this->db->order_by($orderby);
        }

        //模糊查询
        if(!empty($like)){
            $this->_like($like);
        }

        //分组
        if(!empty($groupby)){
            $this->db->group_by($groupby);
        }

        //过滤
        if(!empty($having)){
            $this->db->having($having);
        }

        //分页
        if(isset($offset) && isset($limit)){
            $this->db->limit($limit, $offset);
        }

        //表
        $this->db->from($table);

        //关联
        if(is_array($join) && count($join) > 0){
            $this->_join($join);
        }

        //获取数据
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
	 *@youyiyiper
     * 查：返回数据方式二
     *
     * @param  array  $param   查询的条件
     * @param  bool   $find    是否查询单条数据， FLASE 为 多条； TRUE 为 单条
     * @return 返回数组（array），为空返回空数组；操作错误则返回false
     */
    public function get_list2($param = array(), $find = FALSE){
        if(empty($param)){
			return FALSE;
		}

        /*方式一
    		$act_arr = array('table','where','field','orderby','offset','limit','join','like','groupby','having');
            foreach ($act_arr as $key => $value) {
                isset($param[$value]) ? $$value = $param[$value]: $$value = '';
            }
        */

		//方式二
        extract($param);

        //表名必须存在 并且 不能为空
        if(empty($table)){
			return FALSE;
		}

        //字段
        if(empty($field)){
            $field = "*";
        }

        //条件
        if(!empty($where)){
            $this->db->where($where);
        }

        //查询
        $this->db->select($field);

        if(isset($offset) && isset($limit)){
            $this->db->limit($limit, $offset);
        }

        if(!empty($orderby)){
            $this->db->order_by($orderby);
        }

        //关联关系，二维数组，可查看下面 join($join) 方法
        if(!empty($join)){
            $this->_join($join);
        }

        if(!empty($like)){
            $this->_like($like);
        }

        if(!empty($groupby)){
            $this->db->group_by($groupby);
        }

        if(!empty($having)){
            $this->db->having($having);
        }

        $this->db->from($table);

        if($find == FALSE){
            $result = $this->db->get()->result_array();
        }elseif($find == TRUE){
            $result = $this->db->get()->row_array();
        }

        return $result;
    }

    /**
	 *@youyiyiper
     *查：得到查询总数
	 *
     * @param   string      $table      表名名称
     * @param               $where      查询条件，可数组可字符串
     * @param   array       $join       关联关系，二维数组，可查看下面 _join($join) 方法
     * @param   array       $like       模糊查询  二维数组
     * @return  array/bool              返回查询总数
     */
    public function get_count($table = '', $where = '', $join = array(), $like = array(), $groupby = '', $having = ''){
        
        if(empty($table)){
            return FALSE;
        }

        if(!empty($where)){
            $this->db->where($where);
        }

        if(is_array($join) && count($join) > 0){
            $this->_join($join);
        }

        if(is_array($like) && count($like) > 0){
            $this->_like($like);
        }

        if(!empty($groupby)){
            $this->db->group_by($groupby);
        }

        if(!empty($having)){
            $this->db->having($having);
        }
        
        $this->db->from($table);
        
        return $this->db->count_all_results();
    }

    /**
     * @youyiyiper
	 * 查：得到查询总数
	 *
     * @param  array  $param   查询的条件
     * @return 返回查询总数
     */
    public function get_count2($param = array()){

        if(empty($param)){
            return FALSE;
        }

        /*方式一
    		$act_arr = array('table','where','having','join','groupby', 'like');
            foreach ($act_arr as $key => $value) {
                isset($param[$value]) ? $$value = $param[$value] : $$value = '';
            }
		*/

		//方式二
        extract($param);

        //表名必须存在 并且 不能为空
        if(empty($table)){
			return FALSE;
		}

        if(!empty($where)){
            $this->db->where($where);
        }

        if(!empty($like)){
            $this-_like($like);
        }

        //关联关系，二维数组，可查看下面 _join($join) 方法
        if(isset($join) && !empty($join)){
            $this->_join($join);
        }

        

        if(!empty($groupby)){
            $this->db->group_by($groupby);
        }

        if(!empty($having)){
            $this->db->having($having);
        }

        $this->db->from($table);

        return $this->db->count_all_results();
    }

    /**
     * @youyiyiper
     * 查：返回单条数据
     * 
     * @param   string      $table      表名名称
     * @param               $where      查询条件，可数组可字符串
     * @param   string      $field      查询字段，默认查询所有 *
     * @param   string      $orderby    排序
     * @param   array       $join       关联关系，二维数组，可查看下面 join($join) 方法
     * @param   array       $like       模糊查询
     * @return  mixed                   返回单条数据，为空返回空数组；操作错误则返回false
     */
    public function get_info($table = '', $where = '', $field = '*', $orderby = '', $join = array(), $like =array(), $groupby = '', $having = ''){
        if($table==''){
            return FALSE;
        }
        
        if(!empty($where)){
            $this->db->where($where);
        }

        $this->db->select($field);

        if(!empty($orderby)){
            $this->db->order_by($orderby);
        }

        if(is_array($join) && count($join) > 0){
            $this->_join($join);
        }

        if(is_array($like) && count($like) > 0){
            $this->_like($like);
        }

        if(!empty($groupby)){
            $this->db->group_by($groupby);
        }

        if(!empty($having)){
            $this->db->having($having);
        }

        $this->db->limit(1);

        $query = $this->db->get($table);

        $result = $query->row_array();

        return $result;
    }

    /**
     * join 关联语句拼接
     * @param $join 二维数组
     */
    protected function _join($join){
        foreach ($join as $key => $row) {
            /**
             * $row[0] 为 关联表名
             * $row[1] 为 关联条件
             * $row[2] 为 JOIN的类型，可选项包括： left, right, outer, inner, left outer, 以及 right outer
             */
            $join_type = isset($row[2]) ? $row[2] : 'inner';
            $this->db->join($row[0], $row[1], $join_type);
        }
    }

    /**
     * like 模糊查询
     * @param $like 二维数组
     */
    protected function _like($like){
        foreach ($like as $key => $row) {
            /**
             * $row[0] 为 查询表名
             * $row[1] 为 关联条件
             * $row[2] 为 like的类型，可选项包括： before,after
             */
            $like_type = isset($row[2]) ? $row[2] : 'after';
            $this->db->like($row[0], $row[1], $like_type);
        }
    }

    /**
	 *@youyiyiper
	 *
     * 输出最后操作数据库语句
     */
    protected function _last_query(){
        return $this->db->last_query();
    }
}
