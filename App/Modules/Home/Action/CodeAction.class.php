<?php

/**
 * 	微斯咖前台——微主页
 */
class CodeAction extends Action {

    //
    public function lunpan() {
        $code = I('code');
        
        session('sale_code',$code);
        $url = 'http://'.C('YM_DOMAIN').'/sale/index/lunpan';
        
        header('Location:'.$url);
        
    }
    
    

}

?>