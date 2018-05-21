<?php

/**
 *      地址管理
 */

class AddressAction extends CommonAction
{
    private $model;
    public function _initialize() {
        parent::_initialize();
        $this->model = M('address');
    }

    //地址管理
    public function address_detail() {
        $distributor_obj = M('distributor');

        $managerid = $this->uid;

        $return_url = I('return_url');

        $condition_man = array(
            'id'    =>  $managerid,
        );

        $condition = array(
            'user_id'    =>  $managerid,
        );
        $manager = $distributor_obj->where($condition_man)->find();

        $list = $this->model->where( $condition)->select();

        $this->return_url = $return_url;
        $this->manager = $manager;
        $this->list = $list;
        $this->return_url = $this->get_base_url(I('return_url'));
        $this->base_return_url = I('return_url');
        $this->display();
    }

    //地址添加
    public function address_add()
    {
        $this->return_url = $this->get_base_url(I('return_url'));
        $this->display();
    }

    public function address_insert()
    {
//        $address = I('post.province');
        $address = I('post.probably_address');
        $arr = explode(' ', $address);
        $phone=trim(I('phone'));
        $name=trim(I('name'));
        if(empty($name)){
            $response['status'] = 5;
            $response['message'] ="收货人名字不能为空！";
            return  $this->ajaxReturn($response,'json');
        }
        if(empty($phone)){
            $response['status'] = 2;
            $response['message'] ="手机号码不能为空！";
            return  $this->ajaxReturn($response,'json');
        }
        elseif(strlen($phone) != '11'){
            $response['status'] = 4;
            $response['message'] ="手机号码长度应为11位！";
            return  $this->ajaxReturn($response,'json');
        }
        elseif(!(preg_match('/^0?(13|14|15|17|18)[0-9]{9}$/', $phone))){
            $response['status'] = 3;
            $response['message'] ="手机号码格式不正确！";
            return  $this->ajaxReturn($response,'json');
        }

        $addres=trim(I('post.address'));
        if(empty($addres) || empty($arr)){
            $response['status'] = 6;
            $response['message'] ="地址不能为空！";
            return  $this->ajaxReturn($response,'json');
        }
        $return_url = I('return_url');
        $default = 0;
        if ($return_url) {
            $this->model->where(['user_id' => $this->uid])->save(['default' => 0]);
            $default = 1;
        }
        if($arr[2]==""){
            $data = array(
                'user_id'=>$this->uid,
                'name' => I('post.name'),
                'phone' => I('post.phone'),
                'province' => $arr[0],
                'city'=>$arr[0],
                'area'=>$arr[1],
                'address'=>I('post.address'),
                'default'=>$default,
                'add_time' => time()
            );
            $res = $this->model->add($data);
        }else{
            $data = array(
                'user_id'=>$this->uid,
                'name' => I('post.name'),
                'phone' => I('post.phone'),
                'province' => $arr[0],
                'city'=>$arr[1],
                'area'=>$arr[2],
                'address'=>I('post.address'),
                'default'=>$default,
                'add_time' => time()
            );
            $res = $this->model->add($data);
        }

        if ($res) {
            $response['status'] = 1;
            $response['message'] ="添加地址成功！" ;
            return  $this->ajaxReturn($response,'json');
        } else {
            setLog('sql:'.$this->model->getDbError().',2:'.$this->model->getLastSql(),'address_insert');
            $response['status'] = 0;
            $response['message'] ="添加失败！";
            return  $this->ajaxReturn($response,'json');
        }
    }

    //地址编辑
    public function address_edit() {
        $managerid = $this->uid;

        $id = $_GET['id'];

        $condition = array(
            'user_id'    =>  $managerid,
            'id'    =>  $id,
        );

        $list = $this->model->order('id desc')->where($condition)->find();
        $arr=$list['province'].' '.$list['city'].' '.$list['area'];
        $this->list = $list;
        $this->arr = $arr;
        $this->display();
    }


    //编辑地址
    public function address_edit_post(){

        $managerid = $this->uid;
        $id = I('post.id');
        $name = trim(I('name'));
        $phone = trim(I('phone'));
        $address = trim(I('address'));
        $default=trim(I('default'));
//        $province = I('province')
        $province = I('probably_address');
        $arr=explode(' ',$province);
        if(empty($name)){
            $response['status'] = 5;
            $response['message'] ="收货人名字不能为空！";
            return  $this->ajaxReturn($response,'json');
        }
        if(empty($phone)){
            $response['status'] = 2;
            $response['message'] ="手机号码不能为空！";
            return  $this->ajaxReturn($response,'json');
        }
        elseif(strlen($phone) != '11'){
            $response['status'] = 4;
            $response['message'] ="手机号码长度应为11位！";
            return  $this->ajaxReturn($response,'json');
        }
        elseif(!(preg_match('/^0?(13|14|15|17|18)[0-9]{9}$/', $phone))){
            $response['status'] = 3;
            $response['message'] ="手机号码格式不正确！";
            return  $this->ajaxReturn($response,'json');
        }
        $addres=trim(I('post.address'));
        if(empty($addres) || empty($arr)){
            $response['status'] = 6;
            $response['message'] ="地址不能为空！";
            return  $this->ajaxReturn($response,'json');
        }

        if($arr[2]==""){
            $save_info = array(

                'name'  =>  $name,
                'phone' =>  $phone,
                'province' =>  $arr[0],
                'city' =>  $arr[0],
                'area'  =>    $arr[1],
                'address' =>  $address,
                'default' =>$default,
                'add_time'=>time()
            );
            $condition = array(
                'user_id'    =>  $managerid,
                'id'    =>  $id,
            );

            $save_res = $this->model->where($condition)->save($save_info);

        }else{
            $save_info = array(

                'name'  =>  $name,
                'phone' =>  $phone,
                'province' =>  $arr[0],
                'city' =>  $arr[1],
                'area'  =>    $arr[2],
                'address' =>  $address,
                'default' =>$default,
                'add_time'=>time()
            );
            $condition = array(
                'user_id'    =>  $managerid,
                'id'    =>  $id,
            );

            $save_res = $this->model->where($condition)->save($save_info);

        }

        if( $save_res ){
            $response['status'] = 1;
            $response['message'] ="修改地址成功！" ;
            return  $this->ajaxReturn($response,'json');
        }
        else{
            $response['status'] = 0;
            $response['message'] ="修改地址失败！" ;
            return  $this->ajaxReturn($response,'json');

        }

    }
    //修改默认地址状态
    public function set_address_default(){
        if(!IS_AJAX){
            return FALSE;
        }
        $id=I('post.address_id');
        $condition['user_id'] = $this->uid;
        //如果更改为默认地址，需要把现有默认的改为非默认地址
        $this->model->where($condition)->save(['default' => 0]);

        $condition['id'] = $id;
        $res = $this->model->where($condition)->save(['default' => 1]);
        $this->ajaxReturn($res, 'json');
    }


    //删除地址
    public function address_del(){
        if( !IS_AJAX ){
            return FALSE;
        }
        $id = I('post.id');
        $del_res = $this->model->delete($id);

        if( $del_res ){
            $response['status'] = 1;
            $response['message'] ="删除地址成功！" ;
            return  $this->ajaxReturn($response,'json');
        }
        else{
            $response['status'] = 0;
            $response['message'] ="删除地址失败！" ;
            return  $this->ajaxReturn($response,'json');
        }
    }

}

?>