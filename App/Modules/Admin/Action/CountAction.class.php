<?php

/**
 * 	微斯咖经销商后台
 */
class CountAction extends CommonAction {

    
    public function index() {
        $distributor_obj = M('distributor');
        
        
    	$managerid = session('managerid');
        
        import('Lib.Action.User','App');
        $User = new User();
        
        $month = date('Ym');
        
        $condition = array(
            'month' =>  $month
        );
        
        $user_team_count_info = $User->get_user_team_count($managerid,$condition);
        
        $count_info = array();
        if( $user_team_count_info['code'] == 1 ){
            $count_info = $user_team_count_info['result'];
        }
        
        $last_month = date("Y-m",mktime(0, 0 , 0,date("m")-1,1,date("Y")));
        
        $condition_last_month = array(
            'month' =>  $last_month
        );
        
        $last_month_user_team_count_info = $User->get_user_team_count($managerid,$condition_last_month);
        
        $last_month_count_info = array();
        if( $user_team_count_info['code'] == 1 ){
            $last_month_count_info = $last_month_user_team_count_info['result'];
        }
        
        
        $this->count_info = $count_info;
        $this->last_month_count_info = $last_month_count_info;
        $this->display();
    }
    
    
    
    

}

?>