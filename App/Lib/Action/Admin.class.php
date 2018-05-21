<?php
//后台管理的模块化代码
header("Content-Type: text/html; charset=utf-8");


class Admin {
    
    public $is_add_log = TRUE;//是否添加日志
    
    public $admin_auth = [
        '1' =>  '权限管理',
        '2' =>  '代理查看管理',
        '15' =>  '代理操作管理',
        '13' =>  '代理审核管理',
        
        '5' =>  '虚拟币管理',
        '16' =>  '虚拟币审核管理',
        
        '3' =>  '代理商城管理',
        '17' =>  '代理商城产品属性管理',
        '18' =>  '代理商城产品属性管理',
        
        '4' =>  '积分规则管理',
        '19' =>  '积分订单管理',
        '20' =>  '积分订单管理',
        
        '6' =>  '出库管理',
        '7' =>  '市场营销管理',
        '8' =>  '微官网管理',
        '9' =>  '返利管理',
        '10' =>  '品牌商城管理',
//        '11' =>  '营销活动管理',
        '12'=>  '数据分析管理',
        '14'  => '库存下单管理',
        
        '21'  => '系统配置',
    ];
    
    //权限相关对应的module
    public $admin_auth_module = [
        '1' =>  'admin',
        '2' =>  'manager,upgrade',
        '15' =>  'manager,regulations,info',
        '13' =>  'manager',
        
        '5' =>  'funds',
        '16' =>  'funds',
        
        '3' =>  'order,inform',
        '17' =>  'sku',
        '18' =>  'shipping',
        
        '4' =>  'integral',
        '19' =>  'integralorder',
        '20' =>  'integraltemplet',
        
        '6' =>  'stock',
        '7' =>  'market',
        '8' =>  'publicity,aptitude,goods,info',
        '9' =>  'rebate,newrebate',
        '10' =>  'malltemplet,mallorder',
//        '11' =>  'sale',
        '12'=>  'analysis',
        '14'  => 'stockorder',
        
        '21'  => 'webset',
    ];
    
    //例外的权限
    //首页、用户手册、发展链接
    public $admin_auth_extra = [
        'index','user','templet','info','security','admin'
    ];
    
    
    /**
     * 架构函数
     */
    public function __construct() {
        $FUNCTION_MODULE = C('FUNCTION_MODULE');
        
        if( $FUNCTION_MODULE['MONEY'] != 1 ){
            unset($this->admin_auth[5]);
        }
        elseif( $FUNCTION_MODULE['INTEGRAL_SHOP'] != 1 ){
            unset($this->admin_auth[4]);
        }
        elseif( $FUNCTION_MODULE['MALL_SHOP'] != 1 ){
            unset($this->admin_auth[10]);
        }
        elseif( $FUNCTION_MODULE['STOCK'] != 1 ){
            unset($this->admin_auth[6]);
        }
        elseif( $FUNCTION_MODULE['MARKET'] != 1 ){
            unset($this->admin_auth[7]);
        }
        elseif( $FUNCTION_MODULE['GW'] != 1 ){
            unset($this->admin_auth[8]);
        }
        elseif( $FUNCTION_MODULE['STOCK_ORDER'] != 1 ){
            unset($this->admin_auth[13]);
        }
        
    }
    
    
    
    //添加后台操作日志
    public function add_active_log($aid,$log){


        if( !$this->is_add_log ){
            return TRUE;
        }
        
        
        $cur_url = __SELF__;//当前的URL地址

        if( $aid == NULL || empty($log) ){
            return FALSE;
        }

        $active_log_obj = M('admin_active_log');
        
        
        $add_info = array(
            'aid'           =>  $aid,
            'log'           =>  $log,
            'active_url'    =>  $cur_url,
            'created'       =>  time(),
        );

        $result = $active_log_obj->add($add_info);
        
//        if( $result ){
//            return 1;
//        }
//        else{
//            return $active_log_obj->getDbError();
//        }
        
        
        return $result;
    }//end func add_active_log
    
    //新项目登陆viskaxitong账号初始化一些数据
    public function init_data() {
        //产品属性
        $data = [
            [
                'name'  =>  '款式',
            ],
            [
                'name'  =>  '功效',
            ],
        ];
        M('templet_property')->addAll($data);
        
        //升级说明
        $level_name = C('LEVEL_NAME');
        foreach ($level_name as $k => $v) {
           $option[] = [
               'level' => $k,
               'money' => 0,
               'desc' => '请设置升级支付金额',
               'created' => time(),
           ]; 
        }
        M('distributor_upgrade_desc')->addAll($option);
    }
    
    
    
}