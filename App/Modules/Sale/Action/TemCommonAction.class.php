<?php 

//临时的common
class TemCommonAction extends Action {
    protected $uid;
    protected $activity;
    protected $openid;


    public function _initialize() {
        $the_module_name = strtolower(MODULE_NAME);
        $the_action_name = strtolower(ACTION_NAME);
        $share = I('share');
        
        $this->activity = C('INTEGRAL');
        $wechatinfo = $_SESSION['wechatinfo'];
        $this->openid = empty($_SESSION['oid'])?$wechatinfo['openid']:$_SESSION['oid'];
        session('oid', $this->openid);
        $mall_login_url = __GROUP__.'/mall/login';
        
        
        
        
        if( !empty($this->openid) && $the_action_name != 'login' ){
            session('oid',$this->openid);
            
            $distributor_obj = M('distributor');
            $condition = [
                'openid'    =>  $this->openid,
            ];
            $dis_info = $distributor_obj->where($condition)->find();
            
            if( empty($dis_info)){
//                $return_url = base64_encode($mall_login_url);
//              $this->redirect(__APP__.'/admin/login/loginout?return_url='.$return_url);
//              $this->redirect(__GROUP__.'/mall/login');
                if( $the_module_name != 'mall' ){
                    session('oid',null);
                }
//              $this->msg ="当前微信未注册，请注册后登陆!";
//              $this->redirect(__GROUP__.'/mall/login');
            }
            
            $this->uid = $dis_info['id'];
            $this->manager = $dis_info;
        }
        
        if( ($the_module_name != 'mall') && empty($this->manager) ){
            if($the_module_name != 'shop'){
                $this->redirect($mall_login_url);exit();
            }
        }
        
        
        
        if( (!empty($share)||!empty($uid)) && empty($this->manager) ){
            cookie('share',$share);
            cookie('uid',$uid); 
        }
        
        $this->sharelink = $this->encode_share();
        //$this->openid = $dis_info['openid'];
        $this->level_num = C('LEVEL_NUM');
    }
    
    
    
    
    //编码分享链接
    protected function encode_share(){
        
        if( empty($this->uid) ){
            return FALSE;
        }
        
        $deadline_time = time()+3*24*60*60;//默认三天
        
        $share_info = [
            'cur_id'    =>  $this->uid,
            'pid'       =>  $this->uid,
            'deadline'  =>  $deadline_time,
        ];
        
        $share_info = serialize($share_info);
        
        return tiriEncode($share_info);
    }//end func encode_share
    
    
        protected function encode_uid(){
        if(empty($this->uid)){
            return FALSE;
        }
        
        $uid_info = [
            'uid' => $this->uid
        ];
        
        $uid_info = serialize($uid_info);
        
        return tiriEncode($uid_info);
        
    }
    
    protected function decode_uid($str){
        if(empty($str)){
            return FALSE;
        }
        
        $uif_info = tiriDecode($str);
        
        return unserialize($uif_info);
    }
    
    
    //检查分享链接
    protected function check_share_link($share){
        
        if( empty($share) ){
            return FALSE;
        }
        
        $decode_share = tiriDecode($share);
        $decode_share = unserialize($decode_share);
        
        $cur_id = isset($decode_share['cur_id'])?$decode_share['cur_id']:'';
        
        if( $cur_id != $this->uid ){
            return FALSE;
        }
        
        $old_deadline = $decode_share['deadline'];
        $cur_deadline = time() + 24*60*60;
        
        if( $old_deadline < $cur_deadline ){
            return FALSE;
        }
        
        
        return TRUE;
    }//end func check_share_link
   
    protected function check_uid_link($uid){
       if(empty($uid)){
           return FALSE;
       }
       
       $decode_uid = tiriDecode($uid);
       $decode_uid = unserialize($decode_uid);
       $distributor = M('distributor');
       
       $id = $decode_uid['uid'];
       
       if(!empty($id)){
           $res =  $distributor->where(['id'=>$id])->find();
           if($res){
               return TRUE;
           }else{
               return FALSE;
           }
       }
       return FALSE;
    }
   
   
    /**
     * 检查用户
     * @param type $openid
     * @param type $wechatinfo
     * @return string
     */
    public function check_user($openid,$wechatinfo,$share=''){
        
        $distributor_obj = M('distributor');
        
        if( empty($openid) ){
            $openid = $wechatinfo['openid'];
        }
        
        $condition = [
            'openid'    =>  $openid,
        ];
        
        $dis_info = $distributor_obj->where($condition)->find();
        
        if( !empty($dis_info) ){
            $return_result = [
                'code'      =>  1,
                'msg'       =>  '获取成功！',
                'dis_info'  =>  $dis_info,
            ];
            return $return_result;
        }
        
        
        $decode_share = [];
        if( !empty($share) ){
            $decode_share = tiriDecode($share);
            $decode_share = unserialize($decode_share);
        }
        
        if( !empty($share) && empty($decode_share['pid']) ){
            $return_result = [
                'code'      =>  2,
                'msg'       =>  '分销链接没有正常获取到用户信息！',
                'dis_info'  =>  $dis_info,
            ];
            return;
        }
        
        
        $LEVEL_NUM = C('LEVEL_NUM');
        
//        import('Lib.Action.User','App');
//        $User = new User();
        
        
        
        $audited = 1;
        $pid = isset($decode_share['pid'])?$decode_share['pid']:0;
        $deadline = isset($decode_share['deadline'])?$decode_share['deadline']:0;
        
        if( !empty($deadline) && $deadline < time() ){
            $return_result = [
                'code'      =>  2,
                'msg'       =>  '该链接已经过期失效！',
                'dis_info'  =>  $dis_info,
            ];
            return $return_result;
        }
        
        
//        $info = [
//            'openid'        =>  $openid,
//            'headimgurl'    =>  $wechatinfo['headimgurl'],
//            'pid'           =>  $pid,
//            'nickname'      =>  $wechatinfo['nickname'],
//            'level'         =>  $LEVEL_NUM,
//            'name'          =>  $wechatinfo['nickname'],
//            'wechatnum'     =>  $wechatinfo['nickname'].'_'.rand(10,99).time().'auto',
//            'phone'         =>  '',
//            'email'         =>  '',
//            'idennum'       =>  '',
//            'address'       =>  $wechatinfo['country'].'-'.$wechatinfo['province'].'-'.$wechatinfo['city'],
//            'idennumimg'    =>  '',
//            'liveimg'       =>  '',
//            'password'      =>  '123456',
//            'audited'       =>  $audited,
//        ];
//        
//        $add_result = $User->add($info,$for='sale');
//        
//        if( $add_result['status'] != 1 ){
//            return $add_result;
//        }
//        
//        
//        $condition = [
//            'openid'    =>  $openid,
//        ];
//        
//        $dis_info = $distributor_obj->where($condition)->find();
        
        $return_result = [
            'code'      =>  1,
            'msg'       =>  '获取成功！',
            'dis_info'  =>  $dis_info,
        ];
        return $return_result;
    }//end func check_user
    
    
    
    public function base_url($array){
        $base_str = base64_encode($array);
        return $base_str;
    }
    
    public function get_base_url($base_str){
        $base_url = base64_decode($base_str);
        return $base_url;
    }
    
    
    //获取优惠商城的分享二维码链接
    public function get_mall_encode_link(){
        
        if( !IS_AJAX ){
            return FALSE;
        }
        
        $type = trim(I('type'));
        $code = $this->encode_share();
        
        $YM_DOMAIN = C('YM_DOMAIN');
        
        if( $type == 'mall' ){
            $mall_url = 'http://'.$YM_DOMAIN.'/sale/mall/index?share='.$code;
        }
        else{
            $mall_url = 'http://'.$YM_DOMAIN.'/sale/integralorder/index?share='.$code;
        }
        
        
        $result = [
            'code'  =>  1,
            'msg'   =>  '获取成功！',
            'link'  =>  $mall_url,
        ];
        $this->ajaxReturn($result);
    }//end func get_mall_encode_link
    
    
    
}

 ?>