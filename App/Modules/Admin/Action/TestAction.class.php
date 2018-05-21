<?php

/**
 * 	微斯咖经销商后台——首页
 */
class TestAction extends Action {

    //经销商后台首页
    public function index() {
        
        checkAuth('getinfo');
    }
    
    
    public function test(){
        $return_url = "http://" . C('YM_DOMAIN') . "/Admin/test/test";
        if( $_SESSION['login'] != 'yes' ){
            checkAuth('apply', I('get.ct'),'',$return_url);
        }
        
        
        print_r($_SESSION['sex']);
        
        session('login','no');
    }
    
    
    
    

}

?>