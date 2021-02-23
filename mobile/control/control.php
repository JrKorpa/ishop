<?php
/**
 * mobile父类
 *
 * @ (c) 2015-2018 kelan Inc. (http://官网)
 * @license    http://www.官网
 * @link       交流群号：官网群
 * @since      提供技术支持 授权请购买正版授权
 */



defined('INTELLIGENT_SYS') or exit('Access Invalid!');

/********************************** 前台control父类 **********************************************/

class mobileControl{

    //客户端类型
    protected $client_type_array = array('android', 'wap', 'wechat', 'ios', 'windows');
    //列表默认分页数
    protected $page = 5;
    
    const CACHE_TOKEN_KEY_PREFIX = '_TOKEN-';
    
    protected $member_info = array();
    protected $store_info = array();

    public function __construct() {
        Language::read('mobile');

        //分页数处理
        $page = intval($_GET['page']);
        if($page > 0) {
            $this->page = $page;
        }
        
        $this->loadCurrenUser();
        $this->loadCurrentStore();
    }
    
    protected function loadCurrenUser() {
        $key = $_POST['key'];
        if (empty($key)) {
            $key = $_GET['key'];
        }
        
        if (empty($key)) {
            return;
        }
        
        $this->member_info = rkcache(self::CACHE_TOKEN_KEY_PREFIX.$key);
        if (empty($this->member_info)) {

            $mb_user_token_info =  Model('mb_user_token')->getMbUserTokenInfoByToken($key);
            if (empty($mb_user_token_info)) {
                return;
            } 
            
            $this->fullfillMemberInfo($mb_user_token_info);
        }
        
        // 设置seller group 信息
        if (!empty($this->member_info['seller_group_id'])) {
            $seller_group_key = 'seller_group_'.$this->member_info['seller_group_id'];
            
            $this->member_info['seller_group'] = rkcache($seller_group_key);
            if (empty($this->member_info['seller_group'])) {
                
                $this->member_info['seller_group'] = Model('seller_group')->getSellerGroupInfo(array('group_id' => $seller_info['seller_group_id']));
                wkcache($seller_group_key, $this->member_info['seller_group'], 8 * 3600);
            }
        } else {
            $this->member_info['seller_group'] = [];
        }
    }
    
    protected function fullfillMemberInfo($mb_user_token_info) {
        
        $model_member = Model('member');
        $this->member_info = $model_member->getMemberInfoByID($mb_user_token_info['member_id']);
        
        $this->member_info['client_type'] = $mb_user_token_info['client_type'];
        $this->member_info['openid'] = $mb_user_token_info['openid'];
        $this->member_info['token'] = $mb_user_token_info['token'];
        $level_name = $model_member->getOneMemberGrade($mb_user_token_info['member_id']);
        $this->member_info['level_name'] = $level_name['level_name'];
        
        //设置卖家信息
        $seller_info = Model('seller')->getSellerInfo(array('member_id' => $mb_user_token_info['member_id']));
        if (empty($seller_info)) {
            $this->member_info['store_id'] = 0;
            $this->member_info['store_list'] = '';
            $this->member_info['is_admin'] = 0;
            $this->member_info['seller_id'] = '';
            $this->member_info['seller_group_id'] = '';
        } else {
            $this->member_info['store_id'] = $seller_info['store_id'];
            $this->member_info['is_admin'] = $seller_info['is_admin'];
            $this->member_info['seller_id'] = $seller_info['seller_id'];
            $this->member_info['seller_group_id'] = $seller_info['seller_group_id'];
            
            $store_list = model('seller_store')->get_seller_stores($seller_info['seller_id']);
            $this->member_info['store_list'] = array_column($store_list, 'store_id');
        }        
       
        wkcache(self::CACHE_TOKEN_KEY_PREFIX.$mb_user_token_info['token'], $this->member_info, 8 * 3600);
    }
    
    protected function loadCurrentStore() {
        
        if (empty($this->member_info) || empty($this->member_info['store_id'])) return;
        
        $cache_key = 'store_info'.$this->member_info['store_id'];
        
        $this->store_info = rkcache($cache_key);
        if (empty($this->store_info)) {
            
            $this->store_info = Model('store')->getStoreInfoByID($this->member_info['store_id']);
            if (!empty($this->store_info)) wkcache($cache_key, $this->store_info, 8 * 3600);
        }
    }
    
    protected function logout() {
        dkcache(self::CACHE_TOKEN_KEY_PREFIX.$this->member_info['token']);
    }

    public function changeStore($storeId) {
        $store = model('seller_store')->get_store($this->member_info['seller_id'], $storeId);
        Model('seller')->editSeller(array('store_id'=>$storeId,'seller_group_id'=>$store['seller_group_id'],'is_admin'=>$store['is_admin']), array('seller_id'=>$this->member_info['seller_id']));
        $this->member_info['store_id'] = $storeId;
        $this->member_info['seller_group_id'] = $store['seller_group_id'];
        $this->member_info['is_admin'] = $store['is_admin'];

        wkcache(self::CACHE_TOKEN_KEY_PREFIX.$this->member_info['token'], $this->member_info, 8 * 3600);
        //TODO: 前端页面需要刷新
        output_data('1');
    }
}

class mobileHomeControl extends mobileControl{
    public function __construct() {
        parent::__construct();
    }

    protected function getMemberIdIfExists()
    {
        if (empty($this->member_info)) {
            return 0;
        }

        return $this->member_info['member_id'];
    }
}

class mobileMemberControl extends mobileControl{

    public function __construct() {
        parent::__construct();
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if (strpos($agent, "MicroMessenger") && $_GET["act"]=='auto') {	
			$this->appId ="wxb1a57a4dcb6e4a70";
			$this->appSecret = "c172c3945331f5fc5a97840b9d78df88";			
        }else{
  		
    		if (empty($this->member_info)) {
    		    output_error('请登录', array('login' => '0'));
    		}
        }
    }

    public function getOpenId()
    {
        return $this->member_info['openid'];
    }

    public function setOpenId($openId)
    {
        $this->member_info['openid'] = $openId;
        Model('mb_user_token')->updateMemberOpenId($this->member_info['token'], $openId);
        
        wkcache(self::CACHE_TOKEN_KEY_PREFIX.$this->member_info['token'], $this->member_info, 8 * 3600);
    }


    public function check_seller_limit($perm_key) {
        $model_seller_group = Model('seller_group');
        $seller_group_info = $model_seller_group->getSellerGroupInfo(array('group_id' => $this->member_info['seller_group_id']));
        $seller_limits = explode(',', $seller_group_info['limits']);
        $seller_limits = array_unique($seller_limits);
        if ($this->member_info['is_admin'] !== 1) {
            return in_array($perm_key, $seller_limits);
        }

        return true;
    }

}

class mobileSellerControl extends mobileControl{

    protected $seller_info = array();
    protected $seller_group_info = array();
    protected $member_info = array();
    protected $store_info = array();
    protected $store_grade = array();

    public function __construct() {
        parent::__construct();

        $model_mb_seller_token = Model('mb_seller_token');

        $key = $_POST['key']?$_POST['key']:$_GET['key'];
        if(empty($key)) {
            $key = $_COOKIE['key'];
            if (empty($key)) {
                output_error('请登录', array('login' => '0'));
            }
        }

        $mb_seller_token_info = $model_mb_seller_token->getSellerTokenInfoByToken($key);
        if(empty($mb_seller_token_info)) {
            output_error('请登录', array('login' => '0'));
        }

        $model_seller = Model('seller');
        $model_member = Model('member');
        $model_store = Model('store');
        $model_seller_group = Model('seller_group');

        $this->seller_info = $model_seller->getSellerInfo(array('seller_id' => $mb_seller_token_info['seller_id']));
        $this->member_info = $model_member->getMemberInfoByID($this->seller_info['member_id']);
        $this->store_info = $model_store->getStoreInfoByID($this->seller_info['store_id']);
        $this->seller_group_info = $model_seller_group->getSellerGroupInfo(array('group_id' => $this->seller_info['seller_group_id']));

        // 店铺等级
        if (intval($this->store_info['is_own_shop']) === 1) {
            $this->store_grade = array(
                'sg_id' => '0',
                'sg_name' => '自营店铺专属等级',
                'sg_goods_limit' => '0',
                'sg_album_limit' => '0',
                'sg_space_limit' => '999999999',
                'sg_template_number' => '6',
                'sg_price' => '0.00',
                'sg_description' => '',
                'sg_function' => 'editor_multimedia',
                'sg_sort' => '0',
            );
        } else {
            $store_grade = rkcache('store_grade', true);
            $this->store_grade = $store_grade[$this->store_info['grade_id']];
        }

        if(empty($this->member_info)) {
            output_error('请登录', array('login' => '0'));
        } else {
            $this->seller_info['client_type'] = $mb_seller_token_info['client_type'];
        }
    }
}
