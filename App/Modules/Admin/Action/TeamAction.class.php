<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TeamAction
 *
 * @author ilyzbs
 */
class TeamAction extends CommonAction
{
    //我的团队代理信息
    public function index()
    {

        $this->display();
    }

    /*
     *  @param string $type
     * @param int $page_num
     */
    public function index_ajax()
    {
        if (!IS_AJAX) {
            return FALSE;
        }

        $type = trim(I('type'));
        $keyword=trim(I('post.keyword'));
        $page_num = trim(I('page_num'));
        $page_list_num = trim(I('page_list_num'));


        import('Lib.Action.Team', 'App');
        $User = new Team();


    //    $keyword='000';
    //    $type='under';
    //    $page_num=1;

        if (empty($type)) {
            $return_result = [
                'code' => 2,
                'msg' => '类型获取失败',
            ];
            $this->ajaxReturn($return_result);
        }

        if (empty($page_num)) {
            $return_result = [
                'code' => 3,
                'msg' => '页码获取失败',
            ];
            $this->ajaxReturn($return_result);
        }
        //每页默认为10
        if (empty($page_list_num)) {
            $page_list_num = 10;
        }

        //推介
        if ($type == 'recommend') {
            $page_info = [
                'page_num' => $page_num,
                'page_list_num' => $page_list_num,
            ];
            if(empty($keyword)){
                $condition=[
                    'recommendID' => $this->uid,
                ];
            }else{
                $where['name']  = array('like', "%$keyword%");
                $where['phone']  = array('like',"%$keyword%");
                $where['wechatnum']  = array('eq',"%$keyword%");
                $where['_logic'] = 'or';
                $condition['_complex'] = $where;
                $condition['recommendID'] =$this->uid;
            }
            $condition['audited']=1;
            $result = $User->get_distributor_info($page_info, $condition);

        }
        //直属
        elseif ($type == 'under') {
            $page_info = [
                'page_num' => $page_num,
                'page_list_num' => $page_list_num,
            ];
            if(empty($keyword)){
                $condition=[
                    'pid' => $this->uid,
                ];
            }else{
                $where['name']  = array('like', "%$keyword%");
                $where['phone']  = array('like',"%$keyword%");
                $where['wechatnum']  = array('eq',"%$keyword%");
                $where['_logic'] = 'or';
                $condition['_complex'] = $where;
                $condition['pid'] =$this->uid;
            }
            $condition['audited']=1;
            $result = $User->get_distributor_info($page_info,$condition);
        }

        $return_result = [
            'code' => 1,
            'msg' => '获取成功',
            'info' => $result,
            'id' => $this->uid,

        ];
        $this->ajaxReturn($return_result);
    }

    //我直属的代理
    public function myteam_under()
    {
        $id = $this->uid;
        $model_dirstributor = M('distributor');
        $count = $model_dirstributor->where(array('pid' => $id))->count();
        if ($count > 0) {
            import('ORG.Util.Page');
            $p = new Page($count, 10);
            $limit = $p->firstRow . "," . $p->listRows;
            $p->setConfig('first', "首页");
            $p->setConfig('prev', "上一页");
            $p->setConfig('next', "下一页");
            $p->setConfig('last', "尾页");
            $list = $model_dirstributor->where(array('pid' => $id))->limit($limit)->select();
            $page = $p->show();
            $this->assign('list', $list);
            $this->assign("page", $page);
        }
        $this->display();
    }


    //我的业绩
    public function person_money()
    {
        import('Lib.Action.Team', 'App');
        $team_obj = new Team();
        $date = I('date');
        if ($date) {
            $time = strtotime($date);
            $day = date('Ymd', $time);
            $month = date('Ym', $time);
        } else {
            $day = date('Ymd');
            $month = date('Ym');
        }
        $day_money = $team_obj->get_team_money($this->uid, '', $day);
        //本月业绩及图表数据
        $money = $team_obj->get_graph_money($this->uid, $month);
        $month_money = array_sum($money) . '.00';
        if (IS_AJAX) {
            $data = [
                'day_money' => $day_money,
                'month_money' => $month_money,
                'graph_data' => $money,
                'month' => $month
            ];
            $this->ajaxReturn($data, 'json');
        } else {
            $this->day_money = $day_money;
            //个人业绩
            $this->person_money = $team_obj->get_team_money($this->uid);
            $this->month_money = $month_money;
            $this->graph_data = $money;
            $this->month = $month;
            $this->display();
        }
    }

    //团队业绩
    public function team_money()
    {
        import('Lib.Action.Team', 'App');
        $team_obj = new Team();

        $date = I('date');
        if ($date) {
            $time = strtotime($date);
            $day = date('Ymd', $time);
            $month = date('Ym', $time);
        } else {
            $day = date('Ymd');
            $month = date('Ym');
        }

        $day_money = 0;
        $agent = [];
        //读取缓存团队
        import('Lib.Action.Funds','App');
        $Funds_obj = new Funds();
        $team_path = get_team_path_by_cache();
        if(C('MONEY_COUNT_WAY')|| (!C('MONEY_COUNT_WAY') && !$Funds_obj->is_parent_audit)){
            $uids = $team_obj->get_team_ids($this->uid, $team_path);
        }else{
            $uids=$this->uid;
        }
        //本月业绩
        $month_money = $team_obj->get_team_money($uids, $month);
        //总业绩
        $total_money = $team_obj->get_team_money($uids);
        $users = M('distributor')->field(['id,name,headimgurl'])->where(['id' => ['in', $uids]])->select();
        foreach ($users as $user) {
            if (!isset($agent[$user['id']])) {
                $agent[$user['id']] = $user;
            }
        }
        //每天业绩详情
        $teams_detail = $team_obj->get_team_money_detail($uids, '', $day);
        foreach ($teams_detail as $key => $team) {
            if(!C('MONEY_COUNT_WAY')){
                $day_money += $team['money'];
            }else{
                $day_money += $team['buy_money'];
            }
            $teams_detail[$key]['user_info'] = $agent[$team['uid']];
        }
//        var_dump($teams_detail);die;
        if (IS_AJAX) {
            $data = [
                'day_money' => $day_money,
                'month_money' => $month_money,
                'total_money' => $total_money,
                'detail' => $teams_detail,
                'month' => $month
            ];
            $this->ajaxReturn($data, 'json');
        } else {
            $this->day_money = number_format($day_money,2);
            $this->month_money = $month_money;
            $this->total_money = $total_money;
            $this->detail = $teams_detail;
            $this->month = $month;
            $this->display();
        }
    }

    //代理的详细信息
    public function agency_detail()
    {
        $id = I('get.myteam_id');
        $info = M('distributor')->where(array('id' => $id))->find();
        $this->info = $info;
        $this->uid = $id;
        $this->display();
    }

    //代理的个人业绩ajax
    /*
    *  @param string $month
    * @param string $day
     *
     * @value buy_money
    */
    public function get_person_money()
    {
        if (!IS_AJAX) {
            return FALSE;
        }
        $month = trim(I('month'));
        $day = trim(I('day'));

//         $month='201709';
//        $day='20170912';

        import('Lib.Action.Order', 'App');
        $Order = new Order();
        $uid = $this->uid;
        $condition = [
            'pid' => 0,
            'uid' => $uid,
            'day' => $day,
        ];

        if (!empty($month)) {
            if (empty($day)) {
                $conditio['month'] = $month;
                $day = 0;
                $conditio['day'] = $day;
            } else {
                $conditio['month'] = $month;
                $conditio['day'] = $day;
            }

        } else {
            $result = [
                'code' => 2,
                'msg' => '日期参数错误！',
            ];
            $this->ajaxReturn($result);
        }

        $order_by = '';
        $info = $Order->get_order_count([], $condition, $order_by);

        $result = [
            'code' => 1,
            'msg' => '获取成功',
            'info' => $info,
        ];

        $this->ajaxReturn($result);
    }

    //本月业绩
    /*
    *  @param string $type
    * @param string $month
     *
     * @value buy_money
    */

    public function get_month()
    {

        if (!IS_AJAX) {
            return FALSE;
        }
        $type = trim(I('type'));
        $month = trim(I('month'));
         //$type ='month';
         //$month ='201709';


        import('Lib.Action.Order', 'App');
        $Order = new Order();
        $uid = $this->uid;
        if ($type == 'all') {
            $condition = [
                'pid' => 0,
                'uid' => $uid,
                'day' => 0,
                'month' => $month,
            ];
            if (!empty($month)) {
                if (empty($day)) {
                    $conditio['month'] = $month;
                    $day = 0;
                    $conditio['day'] = $day;
                }

            } else {
                $result = [
                    'code' => 2,
                    'msg' => '月份参数错误！',
                ];
                $this->ajaxReturn($result);
            }
            $order_by = '';
            $info = $Order->get_order_count([], $condition, $order_by);
        } elseif ($type == 'month') {
            if(empty($month)){
                $result = [
                    'code' => 2,
                    'msg' => '月份参数错误！',
                ];
                $this->ajaxReturn($result);
            }
            $condition['day'] = array('gt', 0);
            $condition = [
                'pid' => 0,
                'uid' => $uid,
                'day' => $condition['day'],
                'month' => $month,
            ];
            $order_by = '';
            $info = $Order->get_order_count([], $condition, $order_by);

        }

        $result = [
            'code' => 1,
            'msg' => '获取成功',
            'info' => $info,
        ];

        $this->ajaxReturn($result);
    }

    //上一月业绩
    /*
    * @param string $month
     *
     * @value buy_money
    */

    public function last_month()
    {
        if (!IS_AJAX) {
            return FALSE;
        }
        $month = trim(I('month'));
        // $month='201710';

        import('Lib.Action.Order', 'App');
        $Order = new Order();
        $uid = $this->uid;

        $condition = [
            'pid' => 0,
            'uid' => $uid,
            'day' => 0,
        ];

        if (!empty($month)) {

            $month = ($month . '01');
            $start_time = strtotime($month);
            $start_time_day = strtotime('-1 month', $start_time);
            $month = date("Ym", $start_time_day);

            $condition['month'] = $month;
            $day = 0;
            $condition['day'] = $day;
        } else {
            $result = [
                'code' => 2,
                'msg' => '月份参数错误！',
            ];
            $this->ajaxReturn($result);
        }

        $order_by = '';
        $info = $Order->get_order_count([], $condition, $order_by);

        $result = [
            'code' => 1,
            'msg' => '获取成功',
            'info' => $info,
            'month'=>   $month,
        ];

        $this->ajaxReturn($result);
    }
    
    
    //获取个人订单统计
    public function get_person_count_ajax(){
        
        if( !IS_AJAX ){
            return FALSE;
        }
        
        $pid = I('pid');
        $month = I('month');
        $day = I('day');
        
        if( empty($month) && empty($day) ){
            $return_result = [
                'code'  =>  2,
                'msg'   =>  '月份和时间至少传一个！',
            ];

            $this->ajaxReturn($return_result);
        }
        
        
        import('Lib.Action.Order','App');
        $Order = new Order();
        
        
        $condition = [
            'uid'   => $this->uid,
            'pid'   =>  0,
        ];
        
        if( !empty($pid) ){
            $condition['pid']   =   $pid;
        }
        if( !empty($month) ){
            $condition['month'] =   $month;
        }
        if( !empty($day) ){
            $condition['day'] =   $day;
        }
        
        $info = $Order->get_order_count([],$condition);
        
        
        $return_result = [
            'code'  =>  1,
            'msg'   =>  '接口获取成功！',
            'info'  =>  $info,
        ];
        
        $this->ajaxReturn($return_result);
    }//end func get_person_count_ajax
    
    
    //获取团队订单统计
    public function get_team_count_ajax(){
        
        if( !IS_AJAX ){
            return FALSE;
        }
        
        $pid = I('pid');
        $month = I('month');
        $day = I('day');
        
        if( empty($month) && empty($day) ){
            $return_result = [
                'code'  =>  2,
                'msg'   =>  '月份和时间至少传一个！',
            ];

            $this->ajaxReturn($return_result);
        }
        
        
        import('Lib.Action.Order','App');
        $Order = new Order();
        
        
        $condition = [
            'pid'   =>  0,
        ];
        
        if( !empty($pid) ){
            $condition['pid']   =   $pid;
        }
        if( !empty($month) ){
            $condition['month'] =   $month;
        }
        if( !empty($day) ){
            $condition['day'] =   $day;
        }
        
        $info = $Order->get_team_order_count($this->uid,[],$condition);
        
        
        $return_result = [
            'code'  =>  1,
            'msg'   =>  '接口获取成功！',
            'info'  =>  $info,
        ];
        
        $this->ajaxReturn($return_result);
    }//end func get_team_count_ajax

    //获取团队等级名称
    public function get_levname(){
        if(!IS_AJAX){
             return FALSE;
        }

        $type=trim(I('post.type'));
        $uid = trim(I('uid'));
        
        if(empty($uid)){
          $uid = $this->uid;
        }
        $dis_info = M('distributor')->where(array('id' => $uid))->find();
        $level=$dis_info['level'];


        if(empty($type)){
            $return_result = [
                'code'  =>  2,
                'msg'   =>  '类型获取失败！',
            ];
            $this->ajaxReturn($return_result);
        }
        //推介

        if($type == 'recommendID'){
            $condition=[
                'recommendID' => $uid,
//                'level' => array('egt',$level),
            ];
        }
        //直属
        elseif($type == 'pid'){
            $condition=[
                'pid' => $uid,
                'level' => array('egt',$level),
            ];
        }else{
            $return_result = [
                'code'  =>  3,
                'msg'   =>  '类型不正确！',
            ];
            $this->ajaxReturn($return_result);
        }
        import('Lib.Action.Team', 'App');
        $User = new Team();
        $other['is_group'] = 1;
//        $condition['audited']=1;
        $result = $User->get_dis($condition,$other);
        $return_result = [
            'code'  =>  1,
            'msg'   =>  '接口获取成功！',
            'info'  =>  $result,

        ];
//      set_log($uid);
        $this->ajaxReturn($return_result);
    }

    //查询各级别下面的代理
    public function get_distributor_all_info(){
        if(!IS_AJAX){
            return FALSE;
        }

        import('Lib.Action.Team', 'App');
        $User = new Team();

        $page_num = trim(I('page_num'));
        $page_list_num = trim(I('page_list_num'));
        $level=trim(I('post.level'));
        $type=trim(I('post.type'));
        
        $uid = trim(I('uid'));
        if(empty($uid)){
          $uid = $this -> uid;
        }
//        $page_num=1;
//        $level=1;
//        $type='pid';

        if(empty($type)){
            $return_result = [
                'code'  =>  2,
                'msg'   =>  '类型获取失败！',
            ];
            $this->ajaxReturn($return_result);
        }
        //推介
        if($type == 'recommendID') {
            $condition = [
                'recommendID' => $uid,
                'level' => $level,
            ];
        }
        //直属
        elseif ($type == 'pid'){
            $condition = [
                'pid' => $uid,
                'level' => $level,
            ];
        }

        if (empty($page_num)) {
            $return_result = [
                'code' => 3,
                'msg' => '页码获取失败',
            ];
            $this->ajaxReturn($return_result);
        }
        //每页默认为10
        if (empty($page_list_num)) {
            $page_list_num = 9;
        }
        $page_info = [
            'page_num' => $page_num,
            'page_list_num' => $page_list_num,
        ];
        
//        $condition['audited'] = 1;

        $dis_info=$User->get_distributor_info($page_info,$condition);
        $return_result = [
            'code'  =>  1,
            'msg'   =>  '接口获取成功！',
            'info'  =>  $dis_info,

        ];

        $this->ajaxReturn($return_result);
    }

    //个人业绩接口优化
    public function get_person_money_ajax(){
        if(!IS_AJAX){
            return FALSE;
        }
        $type=trim(I('type'));
        $month=trim(I('month'));
        $day=trim(I('day'));
        import('Lib.Action.Order', 'App');
        $uid = $this->uid;
        $Order = new Order();
        $money_count_way=C('MONEY_COUNT_WAY');
        $money_month_count=M('money_month_count');
//        $type='month_chart';
//        $month=201801;
//        $day=20180111;

        if (empty($type)) {
            $return_result = [
                'code' => 2,
                'msg' => 'type不能为空',
            ];
            $this->ajaxReturn($return_result);
        }
        //今日
        if($type == 'day'){
            if (empty($day)) {
                $return_result = [
                    'code' => 3,
                    'msg' => 'day不能为空',
                ];
                $this->ajaxReturn($return_result);
            }
            if($money_count_way){
                $condition = [
                    'pid' => 0,
                    'uid' => $uid,
                    'day' => $day,
                    'month'=>$month
                ];
                $order_by = '';
                $info = $Order->get_order_count([], $condition, $order_by);
            }else{
                $condition = [
                    'uid' => $uid,
                    'day' => $day,
                    'month'=>$month
                ];
                $info = $money_month_count->where($condition)->sum('money');
                $info=isset($info)?$info:'0.00';
            }

        }
        //类型为month(本月)
        elseif($type == 'month'){
            if (empty($month)) {
                $return_result = [
                    'code' => 4,
                    'msg' => 'month不能为空',
                ];
                $this->ajaxReturn($return_result);
            }
            if(strlen($month) != 6){
                $return_result = [
                    'code' => 5,
                    'msg' => 'month的格式应为201801格式',
                ];
                $this->ajaxReturn($return_result);
            }
            if($money_count_way){
                $condition = [
                    'pid' => 0,
                    'uid' => $uid,
                    'day' => 0,
                    'month' => $month,
                ];
                $order_by = '';
                $info = $Order->get_order_count([], $condition, $order_by);
            }else{
                $condition = [
                    'uid' => $uid,
                    'month'=>$month
                ];
                $info = $money_month_count->where($condition)->sum('money');
                $info=isset($info)?$info:'0.00';
            }

        }
        //上个月
        elseif ($type == 'last'){
            if (empty($month)) {
                $return_result = [
                    'code' => 6,
                    'msg' => 'month不能为空',
                ];
                $this->ajaxReturn($return_result);
            }
            $month = ($month . '01');
            $start_time = strtotime($month);
            $start_time_day = strtotime('-1 month', $start_time);
            $month = date("Ym", $start_time_day);
            if($money_count_way){
                $condition = [
                    'pid' => 0,
                    'uid' => $uid,
                    'day' => 0,
                    'month'=>$month,
                ];
                $order_by = '';
                $info = $Order->get_order_count([], $condition, $order_by);
            }else{
                $condition = [
                    'uid' => $uid,
                    'month'=>$month
                ];
                $info = $money_month_count->where($condition)->sum('money');
                $info=isset($info)?$info:'0.00';
            }
        }
        //这个月的图形显示
        elseif ($type == 'month_chart'){
            if($money_count_way){
                $condition = [
                    'pid' => 0,
                    'uid' => $uid,
                    'day' => array('gt',0),
                    'month' => $month,
                ];
                $order_by = '';
                $info = $Order->get_order_count([], $condition, $order_by);
            }else{
                $condition = [
                    'uid' => $uid,
                    'month'=>$month
                ];
                $info = $money_month_count->where($condition)->select();
            }

        }
        $return_result = [
            'code'  =>  1,
            'msg'   =>  '接口获取成功！',
            'info'  =>  $info,
            'money_count_way'=>$money_count_way

        ];
        $this->ajaxReturn($return_result);
    }

}
