<?php

/**
 *    分销/优惠商城
 */
class MallAction extends TemCommonAction
{

    private $model;
    private $templet_model;
    private $templet_cat_model;
    private $cart_model;
    private $advert;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = M('mall_order');
        $this->templet_model = M('mall_templet');
        $this->templet_cat_model = M('mall_templet_category');
        $this->cart_model = M('mall_order_shopping_cart');
        $this->adv_model = M('mall_advert');
    }

    //下单 new
    public function index()
    {
//        if( !C('IS_TEST') ){
//            echo "<script>alert('暂未开放！');window.location.href='".__APP__."/admin/index'; </script>";
//            return;
//        }


        if (!empty($this->manager)) {
            //更改为其它方式
            $share = I('share');
            $check_share_result = $this->check_share_link($share);

            if (!$check_share_result) {
                $encode_share = $this->encode_share();
                $url = __APP__ . '/sale/mall/index?share=' . $encode_share;
                header('Location:' . $url);
                //            var_dump($check_share_result);
                exit();
            }
        }


        //首页显示的产品
        $condition_product = [
            'active' => 1,
//            'statu' =>1,
        ];
        //首页显示的分类
        $condition_cat = [
            'statu' => 1,
            'pid' => 0,
            'active' => 1,
        ];

        //分类不在首页显示的时候，出现在更多分类的列表中
        $condition_two = [
            'statu' => 0,
            'active' => 1,
            'pid' => 0,
        ];
        $products = $this->templet_model->where($condition_product)->select();
        $catss = $this->templet_cat_model->where($condition_cat)->select();
        $sort_cat = $this->templet_cat_model->where($condition_cat)->order('id desc')->limit(6)->select();
        $this->count = $this->templet_cat_model->where($condition_two)->count();
        import('Lib.Action.Team', 'App');
        $Team = new Team();
        $sort_cat = $Team->sortt($sort_cat);


        //广告图片
        $condition_one = [
            'type' => 1,
            'status' => 1,
        ];

        $condition_two = [
            'type' => 2,
            'status' => 1,
            'malltemplet_category_id' => 0,
        ];
        $adv_info = $this->adv_model->where($condition_one)->order('sequence desc,id desc')->limit(4)->select();
        $adv_hai = $this->adv_model->where($condition_two)->order('sequence desc,id desc')->find();

        $this->products = $products;

        $this->catss = $catss;
        $this->sort_cat = $sort_cat;
        $this->adv_info = $adv_info;
        $this->adv_hai = $adv_hai;

        $this->display();

    }

    //商品详情 new
    public function goods_detail()
    {

        $id = I('id');

        if (!empty($this->manager)) {
            $share = I('share');
            $check_share_result = $this->check_share_link($share);

            if (!$check_share_result) {
                $encode_share = $this->encode_share();
                $url = __APP__ . '/sale/mall/goods_detail?id=' . $id . '&share=' . $encode_share;
                header('Location:' . $url);
                //            var_dump($check_share_result);
                exit();
            }
        }


        $product = $this->templet_model->where(['active' => '1'])->find($id);
        $product['many_image'] = explode(',', $product['many_image']);
        $this->product = $product;

        $this->display();
    }

    //分类
    public function commodity()
    {
//        $where['status'] = '1';
//        $id = I('id');
//        if ($id) {
//            $where['category_id'] = $id;
//        }
//        $products = $this->templet_model->where($where)->select();
        $condition_cat = [
            'statu' => 0,
            'active' => 1,
            'pid' => 0,
        ];
        $catss = $this->templet_cat_model->where($condition_cat)->select();
        $condition = [
            'pid' => array('gt', 0),
            'statu' => 0,
            'active' => 1,
        ];
        $cateres = $this->templet_cat_model->where($condition)->select();

//        $this->products = $products;
        $this->catss = $catss;
        $this->cateres = $cateres;

        $this->display();
    }


    //注册
    public function sign_up()
    {
        if (!IS_AJAX) {
            return FALSE;
        }

        $phone = I('phone');
        $result = [];
        $pid = "0";
        $wechat_info = $_SESSION['usersignup'];
//        var_dump($wechat_info);die;
        $exist_account = M('distributor')->where(array('phone' => $phone))->find();
        if (empty($exist_account)) {

            $share = cookie('share');
            if (!empty($share)) {
                $share_data = decode_share_link($share);
                $pid = $share_data['pid'];
            }
            $apply_info = array(
                'openid' => $wechat_info['openid'],
                'headimgurl' => $wechat_info['headimgurl'],
                'pid' => $pid,
                'nickname' => $wechat_info['nickname'],
                'level' => C('LEVEL_NUM'),
                'name' => $wechat_info['nickname'],
                'wechatnum' => $wechat_info['openid'] . '_' . $pid,
                'phone' => $phone,
                'email' => "",
                'idennum' => "",
                'address' => "",
                'idennumimg' => "",
                'liveimg' => "",
                'province' => $wechat_info['province'],
                'city' => $wechat_info['city'],
                'county' => "",
                'password' => md5(substr($phone, -6)),
            );

            import('Lib.Action.User', 'App');
            $User = new User();
            $result = $User->add($apply_info);

        } else {
            $result = [
                'code' => 3,
                'msg' => '该手机号码已注册',
            ];
        }

        $this->ajaxReturn($result, 'json');
    }

    //登录页
    public function login()
    {

        if (empty($_SESSION['usersignup'])) {
            $return_url = __APP__ . '/sale/mall/login';
            checkAuth('getsalesignup', '', '', $return_url);
        }

        $this->display();
    }

    //检查微信登陆
    public function check_wechat()
    {
        if (!IS_AJAX) {
            return FALSE;
        }
        $result = [];

        $this->openid = $_SESSION['oid'];
        if (empty($this->openid)) {
            //获取到oid为空，直接在微信信息中获取
            $this->openid = $_SESSION['usersignup']['openid'];
            $_SESSION['oid'] = $this->openid;
        }

        if (!empty($this->openid)) {
            $distributor_obj = M('distributor');
            $condition = [
                'openid' => $this->openid,
            ];
            $dis_info = $distributor_obj->where($condition)->field('id')->find();

            if (empty($dis_info)) {
                $result = [
                    'code' => 0,
                    'return_url' => "",
                    'msg' => '该微信未注册，请注册后登陆',
                ];
            } else {
                $result = [
                    'code' => 1,
                    'return_url' => __GROUP__ . '/mall/index',
                    'msg' => '登陆成功',
                ];
            }
        }else {
            $result = [
                'code' => -1,
                'return_url' => "",
                'msg' => '请先关注公众号',
            ]; 
        }

        $this->ajaxReturn($result);
    }

    //用户登录系统
    public function user_login()
    {
        if (!IS_AJAX) {
            return FALSE;
        }

        $account = I('account');
        $password = I('password');

        $result = [];

        $row = M('Distributor')->where(array('phone' => $account))->find();
        if (!$row) {
            $result = [
                'code' => 0,
                'return_url' => "",
                'msg' => '该账号不存在',
            ];
        } else {
            if ($row['password'] == md5($password)) {
//              session('login_status',true);
                session('oid', $row['openid']);

                $result = [
                    'code' => 1,
                    'return_url' => __APP__ . '/sale/mall/index',
                    'msg' => '登录成功'
                ];
            } else {
                $result = [
                    'code' => 2,
                    'return_url' => "",
                    'msg' => '密码错误'
                ];
            }
        }

        $this->ajaxReturn($result);
    }

    //个人信息页
    public function info()
    {

        $openid = $_SESSION['oid'];
        $tb_user = M('distributor');
        $type = trim(I('type'));

        $row = $tb_user->where(['openid' => $openid])->find();

        $rec_id = $row['recommendID'];

        $rec_info = $tb_user->where(['id' => $rec_id])->find();
        if (empty($rec_info)) {
            $rec_info['name'] = '总部';
        }
        $this->idimg = $row['idennumimg'];

        $this->rec_info = $rec_info;

        $this->row = $row;
        $this->type = $type;

        $this->display();
    }

    //修改个人信息
    public function save_info()
    {
        if (!IS_AJAX) {
            return FALSE;
        }
        //获取数据
        $nickname = I('nickname');
        $wechatnum = I('wechatnum');
        $phone = I('phone');
        $city = I('city');
        $province = I('province');
        $county = I('county');
        $idennum = I('idennum');
        $password = I('password');
        $openid = $_SESSION['oid'];
        $type = trim(I('type'));
        $idennumimg = trim(I('idennumimg'));
        $liveimg = trim(I('liveimg'));
        $apply_level = trim(I('level'));
        $IS_SUBMIT_ID_CARD_IMG = trim(I('IS_SUBMIT_ID_CARD_IMG'));

        //初始化数据
        $result = [];
        $distributor = M('distributor');
        $filter = [
            'openid' => $openid
        ];

        $user_info = $distributor->where($filter)->find();
        $uid = $user_info['id'];
        $data = [
            'nickname' => $nickname,
            'wechatnum' => $wechatnum,
            'phone' => $phone,
            'city' => $city,
            'province' => $province,
            'county' => $county,
            'idennum' => $idennum,
        ];
        if(strlen($idennum) != 18){
            $result = [
                'code' => 7,
                'msg' => '请输入18位的身份证号码',
            ];
            $this->ajaxReturn($result);
        }
        if (empty($user_info)) {
            $result = [
                'code' => 0,
                'msg' => '修改失败，没找到该用户信息',
            ];
        } else {
            if (empty($type)) {
                if (!empty($password)) {
                    $data['password'] = md5($password);
                }
                $res = $distributor->where($filter)->save($data);
                if (!empty($res)) {
                    $result = [
                        'code' => 1,
                        'msg' => '修改成功',
                    ];
                } else {
                    $result = [
                        'code' => 2,
                        'msg' => '无需提交修改',
                    ];
                }
            } elseif ($type == 'up') {
                if (empty($nickname) || empty($wechatnum) || empty($phone) || empty($province) || empty($city) || empty($county) || empty($idennum)) {
                    $result = [
                        'code' => 3,
                        'msg' => '个人资料必须全部完善才能提交',
                    ];
                    $this->ajaxReturn($result);
                }
                if ($IS_SUBMIT_ID_CARD_IMG == 2) {
                    if (empty($user_info['idennumimg'])) {
                        if (empty($idennumimg)) {
                            $result = [
                                'code' => 4,
                                'msg' => '请上传照片',
                            ];
                            $this->ajaxReturn($result);
                        } else {
                            $data['idennumimg'] = $idennumimg;
                        }
                    }
                } elseif ($IS_SUBMIT_ID_CARD_IMG == 3) {
                    if (empty($user_info['liveimg'])) {
                        if (empty($liveimg)) {
                            $result = [
                                'code' => 4,
                                'msg' => '请上传照片',
                            ];
                            $this->ajaxReturn($result);
                        }
                    } else {
                        $data['liveimg'] = $liveimg;
                    }
                } elseif ($IS_SUBMIT_ID_CARD_IMG == 1) {
                    if (empty($user_info['idennumimg'])) {
                        if (empty($idennumimg)) {
                            $result = [
                                'code' => 4,
                                'msg' => '请上传照片',
                            ];
                            $this->ajaxReturn($result);
                        } else {
                            $data['idennumimg'] = $idennumimg;
                        }
                    } elseif (empty($user_info['liveimg'])) {
                        if (empty($liveimg)) {
                            $result = [
                                'code' => 4,
                                'msg' => '请上传照片',
                            ];
                            $this->ajaxReturn($result);
                        }
                    } else {
                        $data['liveimg'] = $liveimg;
                    }
                }
            $distributor->where($filter)->save($data);
            //调用升级申请的接口
            import('Lib.Action.User', 'App');
            $User = new User();
            $result = $User->add_upgrade_apply($uid, $user_info, $apply_level);
            }
        }
        $this->ajaxReturn($result);
    }

    //商品详情 new
    public function goods_all()
    {

        $share = I('share');
        $name = I('name');
        $category_id = I('category_id');

        $check_share_result = $this->check_share_link($share);

        if( !$check_share_result ){
            $encode_share = $this->encode_share();
            $url = __APP__.'/sale/mall/goods_all?share='.$encode_share.'&name='.$name.'&category_id='.$category_id;
            header('Location:'.$url);
            exit();
        }



        $condition = [
            'active' => '1',
        ];

        if( !empty($name) ){
            $condition['name'] = ['like',$name];
        }
        if( !empty($category_id) ){
            $condition['category_id'] = $category_id;
        }

//        $price = "price" . $this->manager['level'];
        $product = $this->templet_model->where($condition)->select();
//        $product['price'] = $product[$price];

//        foreach( $product as $k => $v ){
//            $product[$k]['price'] = $v['price'];
//        }

        $this->product = $product;
        $this->display();
    }//end func goods_all

}

?>