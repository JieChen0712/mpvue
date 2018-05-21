<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of team
 *团队相关函数
 * @author ilyzbs
 */
class team {
    private $distributor_model;
    private $count_model;
    private $order_count_model;
    
    private $default_team;
    
    public function __construct() {
        $this->distributor_model = M('distributor');
        $this->order_count_model = M('order_count');
        $this->count_model = M('money_month_count');
        $this->default_team = C('DEFAULT_TEAM');
        
//        if (C('MONEY_COUNT_WAY')) {
//            $this->count_model = M('order_month_count');
//        } else {
//            $this->count_model = M('money_month_count');
//        }
    }

        /**
     * 获取团队id集合
     * @param int $uid
     * @return type
     */
//     public function get_team_ids($uid,$default_team='') {
//         if( empty($default_team) ){
//             $default_team = $this->default_team;
//         }
        
//         $path = $this->distributor_model->where(['id' => $uid])->getField($default_team);
//         $path .= '-'.$uid;
//         $where['is_lowest'] = 1;
//         $where[$default_team] = $path;
        
//         $map['is_lowest'] = 1;
//         $map[$default_team] = ['like', "$path-%"];
//         $a['_complex'] = $where;
//         $condition[] = $a;
//         $condition['_logic'] = 'OR';
//         $condition['_complex'] = $map;
//         $users = $this->distributor_model->where($condition)->field("id,$default_team")->select();
// //        return $this->distributor_model->getLastSql();
//         if(empty($users)) {
//             return [$uid];
//         }
//         $ids = [];
//         foreach ($users as $user) {
//             $arr = explode('-', $user[$default_team]);
//             $arr[] = $user['id'];
//             $ids = array_merge($ids,$arr);
//         }
//         $ids = array_unique($ids);
        
//         //排除上级id
//         $rec_str = $this->distributor_model->where(['id' => $uid])->getField($default_team);
//         $rec_arr = explode('-', $rec_str);
//         foreach ($ids as $k => $id) {
//             if(in_array($id, $rec_arr)) {
//                 unset($ids[$k]);
//             }
//         }
//         return $ids;
//     }

    /**
     * @param int $uid 要查找团队的代理id
     * @param array $users 保存在缓存文件的最底层的is_lowest=1的代理$default_team字段的数据
     * @param int $default_team path/rec_path字段
     * @return array 代理id集合(一维数组)
     */
    public function get_team_ids($uid, $users, $default_team='') {
        if( empty($default_team) ){
            $default_team = $this->default_team;
        }

        if(empty($users)) {
            return [$uid];
        }
        $my = $this->distributor_model->where(['id' => $uid])->field("id,$default_team,is_lowest")->find();
        //判断到是最低级并且没有下级直接返回
//        $count = $this->distributor_model->where(['recommendID' => $my['id']])->count('id');
//        if ($my['is_lowest'] == 1 && $count == 0) {
//            return [$uid];
//        }
        if ($my['is_lowest']) {
            return [$uid];
        }
        $rec_path = $my[$default_team];
        $rec_str = $rec_path;
        $rec_path .= '-'.$uid;
        $result = "";
        //结果团队id集合
        $ids = [];
        //正则匹配出的id字符串集合
        $match_path = [];
        //最底层代理id集合
        $low_ids = [];
        $rule  = "/($rec_path.*)/";
        foreach ($users as $user) {
            preg_match($rule,$user[$default_team],$result);
            if ($result) {
                $match_path[] = $result[0];
                $low_ids[] = $user['id'];
            }
        }
        $match_path = array_unique($match_path);
        foreach ($match_path as $path_str) {
            $arr = explode('-', $path_str);
            foreach ($arr as $v) {
                $ids[$v] = $v;
            }
        }
        $ids = array_merge($ids,$low_ids);
        $ids = array_unique($ids);
        //排除上级id
        $rec_arr = explode('-', $rec_str);
        foreach ($ids as $k => $id) {
            if(in_array($id, $rec_arr)) {
                unset($ids[$k]);
            }
        }
        //自己的id也要在团队里面
        if (!in_array($uid, $ids)) {
            $ids[] = $uid;
        }
//        var_dump($ids);die;
        return $ids;
    }
    
    /**
     * 获取团队/个人业绩
     * @param type $uids
     * @param type $month
     * @return int
     */
    public function get_team_money($uids, $month = "", $day = "") {
        if ($month) {
            $where['month'] = $month;
        }
        if ($day) {
            $where['day'] = $day;
        }
        if (is_array($uids)) {
            //团队业绩
            $where['uid'] = ['in', $uids];
            if( C('MONEY_COUNT_WAY') ){
                $where['day'] = isset($where['day'])?$where['day']:0;
                $where['pid']   =   0;
                $money = $this->order_count_model->where($where)->sum('buy_money');
            }
            else{
                $money = $this->count_model->where($where)->sum('money');
            }

            return empty($money) ? 0.00 : $money;
        } else {
            //个人业绩
            $where['uid'] = $uids;
            $money = $this->count_model->where($where)->sum('money');
            return empty($money) ? 0.00 : $money;
        }
        return 0.00;
    }
    
    
    /**
     * 2017-11-3废弃
     * 获取团队/个人业绩
     * @param type $uids
     * @param type $month
     * @return int
     */
    public function get_team_money_old($uids, $month = "", $day = "") {
        if ($month) {
            $where['month'] = $month;
        }
         if ($day) {
            $where['day'] = $day;
        }
        if (is_array($uids)) {
            //团队业绩
            $where['uid'] = ['in', $uids];
            $money = $this->count_model->where($where)->sum('money');
            return empty($money) ? 0.00 : $money;
        } else {
            //个人业绩
            $where['uid'] = $uids;
            $money = $this->count_model->where($where)->sum('money');
            return empty($money) ? 0.00 : $money;
        }
        return 0.00;
    }
    
    
     /**
     * 获取团队/个人业绩详细数据
     * @param type $uids
     * @param type $month
     * @return int
     */
    public function get_team_money_detail($uids, $month = "", $day = "") {
        if ($month) {
            $where['month'] = $month;
        }
         if ($day) {
            $where['day'] = $day;
        }
        if (is_array($uids)) {
            //团队业绩
            $where['uid'] = ['in', $uids];
            if( C('MONEY_COUNT_WAY') ){
                $where['day'] = isset($where['day'])?$where['day']:0;
                $where['pid']   =   0;
                return $this->order_count_model->where($where)->select();
            }
            else{
                return $this->count_model->where($where)->select();
            }
        } else {
            //个人业绩
            $where['uid'] = $uids;
            return $this->count_model->where($where)->select();
        }
        return null;
    }
    
    //本月业绩(用于图形统计数据)
    public function get_graph_money($uid, $month = "") {
        $money = [];
        if (!$month) {
            $month = date('Ym');
        }
        
        $where['uid'] = $uid;
        $where['month'] = $month;
        
        
        if( C('MONEY_COUNT_WAY') ){
            $money_key = 'buy_money';
            
            $where['pid']   =   0;
            $count = $this->order_count_model->where($where)->select();
        }
        else{
            $money_key = 'money';
            $count = $this->count_model->where($where)->select();
        }
        
        foreach ($count as $v) {
            if (isset($money[$v['day']])) {
                $money[$v['day']] += $v[$money_key];
            } else {
                $money[$v['day']] = $v[$money_key];
            }
        }
        for ($i=1;$i<32;$i++) {
           if ($i<10) {
               $day = $month.'0'.$i;
           } else {
               $day = $month.$i;
           }
           if (!isset($money[$day])) {
               $money[$day] = '0.00';
           }
        }
        ksort($money);
        $data = array_values($money);
        return $data;
    }

    //银行卡
    public function get_distributor_bank($page_info=array(),$condition=array()){


        $distributor_obj = M('distributor_bank');
        import('ORG.Util.Page');


        $list = array();
        $page = '';
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?20:$page_info['page_list_num'];


        $count = $distributor_obj->where($condition)->count();

        if( $count > 0 ){
            if( !empty($page_info) ){

                $page_con = $page_num.','.$page_list_num;

                $list = $distributor_obj->where($condition)->order('id desc')->page($page_con)->select();
            }
            else{
                $list = $distributor_obj->where($condition)->order('id desc')->select();
            }

        }


        if( !empty($page_info) ){
            //*分页显示*
            import('ORG.Util.Page');
            $p = new Page($count, $page_list_num);
            $page = $p->show();
        }


        $return_result = array(
            'list'  =>  $list,
            'page'  =>  $page,
        );

        return $return_result;
    }

//数据根据pid排序
    public function sortt($data,$pid=0,$level=0){
        static $arr=array();
        foreach ($data as $k => $v) {
            if($v['pid']==$pid){
                $v['level']=$level;
                $arr[]=$v;
                $this->sortt($data,$v['id'],$level+1);
            }
        }
        return $arr;

    }

 


    //获取代理等级中文名字和人数信息
    public function get_dis($condition=array(),$other=array()){
        $is_group = isset($other['is_group'])?$other['is_group']:0;
        $list = $this->distributor_model->where($condition)->group('level')->order('level')->select();

        foreach ($list as $v) {
            if (!isset($ids[$v['level']])) {
                $ids[$v['level']] = $v['level'];
                $ids[$v['levname']] = $v['levname'];
                $resultname[]=$ids[$v['levname']];
                $condition['level']=$ids[$v['level']];
                $level_num[] =  $this->distributor_model->where($condition)->count();
            }
        }


        foreach( $list as $k => $v ){
            $lev_num = array_combine($resultname, $level_num);
            $levname =  $v['levname'];
            $level[]=    $v['level'];
            $list_group[$levname][$v['level']] = $level_num[$k];

        }

        if( $is_group){
            $list = $list_group;
        }
        //-----end 整理添加相应其它表的信息-----

        $return_result = array(
            'list'  =>  $list,
        );

        return $return_result;
    }//end func get_dis

    //获取等级下面各代理信息
    public function get_distributor_info($page_info=array(),$condition=array()){

        $list = array();
        $page = '';
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?30:$page_info['page_list_num'];
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];


        //$level_name = C('LEVEL_NAME');
        $level_num = C('LEVEL_NUM');
        $level_num_max = C('LEVEL_NUM_MAX');

        $count = $this->distributor_model->where($condition)->count();

        if( $count > 0 ){

            if( !empty($page_info) ){

                $page_con = $page_num.','.$page_list_num;

                $list = $this->distributor_model->where($condition)->order('convert(name using gb2312) asc')->page($page_con)->cache(true)->select();

            }
            else{
                $list = $this->distributor_model->where($condition)->order('convert(name using gb2312) asc')->cache(true)->select();

            }
            $ids = array();
            foreach( $list as $k => $v ){
                $v_uid = $v['id'];
                if( !isset($ids[$v_uid]) ){
                    $ids[$v_uid] = $v_uid;
                }
            }
            array_values($ids);
            array_unique($ids);
            $condition_dis = array(
                'id'    =>  array('in',$ids),
            );
            $field = 'id,recommendID,level,levname,pid';

            $dis_info = $this->distributor_model->field($field)->where($condition_dis)->select();

            foreach( $dis_info as $k_dis=>$v_dis ){
                $v_dis_id = $v_dis['id'];

                $condition1=[
                    'recommendID' => $v_dis_id,
                ];
                $condition2=[
                    'pid' => $v_dis_id,
                ];

                $dis_recommendID_info=$this->distributor_model->where($condition1)->count();
                $dis_pid_info=$this->distributor_model->where($condition2)->count();

                $dis_key1_info[$v_dis_id] = $dis_recommendID_info;
                $dis_key2_info[$v_dis_id] =$dis_pid_info;

            }

            foreach( $list as $k => $v ){
                $v_uid = $v['id'];
                $list[$k]['dis_info'] = $dis_key1_info[$v_uid];
                $list[$k]['dis_recommendID_count'] = $dis_key1_info[$v_uid];
                $list[$k]['dis_pid_count'] = $dis_key2_info[$v_uid];
                $v_time = $v['time'];
                $list[$k]['time_format'] = date('Y-m-d H:i',$v_time);
            }

            //-----end 整理添加相应其它表的信息-----
        }

        if( !empty($page_info) ){
            //*分页显示*
            import('ORG.Util.Page');
            $p = new Page($count, $page_list_num);
            $page = $p->show();
        }
        $return_result = array(
            'list'  =>  $list,
            'page'  =>  $page,
        );

        return $return_result;
    }//end func get_distributor


    //经销商树状图
    public function dis_tree($page_info,$condition=array()){
        $list = array();
        $page = '';
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?30:$page_info['page_list_num'];
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];

        $YM_DOMAIN = C('YM_DOMAIN');
        $level_num = C('LEVEL_NUM');
        $level_name = C('LEVEL_NAME');

        $count = $this->distributor_model->where($condition)->count();

        if( $count > 0 ){
            if( !empty($page_info) ){
                $page_con = $page_num.','.$page_list_num;
                $list =$this->distributor_model->where($condition)->page($page_con)->select();
            }
            else{
                $list =$this->distributor_model->where($condition)->select();
            }
            foreach ($list as $k => $v) {
                $list[$k]['count'] =$this->distributor_model->where(array('pid' => $list[$k]['id']))->count();
            }
        }

        if( !empty($page_info) ){
            //*分页显示*
            import('ORG.Util.Page');
            $p = new Page($count, $page_list_num);
            $page = $p->show();
        }
        $return_result = array(
            'list'  =>  $list,
            'page'  =>  $page,
            'YM_DOMAIN' => $YM_DOMAIN,
            'level_num' => $level_num,
            'level_name' =>$level_name,
        );

        return $return_result;
    }

    //获取树状图的下属
    public function dis_treedirect($page_info,$condition=array()){
        $list = array();
        $page = '';
        //每页的数量
        $page_list_num = !isset($page_info['page_list_num'])||empty($page_info['page_list_num'])?30:$page_info['page_list_num'];
        //如果页码为空的话默认值为1
        $page_num = !isset($page_info['page_num'])||empty($page_info['page_num'])?1:$page_info['page_num'];

        $count = $this->distributor_model->where($condition)->count();

        if ($count > 0) {

            if( !empty($page_info) ){
                $page_con = $page_num.','.$page_list_num;
                $list =$this->distributor_model->where($condition)->page($page_con)->select();
            }
            else{
                $list =$this->distributor_model->where($condition)->select();
            }
            foreach ($list as $k => $v) {
                $list[$k]['count'] =$this->distributor_model->where(array('pid' => $list[$k]['id']))->count();

                }
        }else{
            $return_result = array(
                'code'  =>  2,
                'msg'  =>  '没下级经销商',
            );
            return $return_result;
        }
        if( !empty($page_info) ){
            //*分页显示*
            import('ORG.Util.Page');
            $p = new Page($count, $page_list_num);
            $page = $p->show();
        }
        $return_result = array(
            'list'  =>  $list,
            'page'  =>  $page,
        );
        return $return_result;
    }

}
