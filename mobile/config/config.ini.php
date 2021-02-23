<?php
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
$wap_site_url = $config['wap_site_url']."/images/xilie/";

//优先顺序从前到后
$config['order_detail_img']=array(
       'style_sn'=> array(
           //Fresh系列四款女戒
           [
               'style_sn_s'=>['KLRW030271','KLRW030272','KLRW030287','KLRW030288'],
               'img_url'=>$wap_site_url."fresh.jpg",
           ],
          //恒久爱系列两对对戒
           [
               'style_sn_s'=>['KLLW029917','KLLM029918','KLLW029964','KLLM029965'],
               'img_url'=>$wap_site_url."everlasting_love.jpg",
           ],
           //旋转皇冠两款女戒
           [
               'style_sn_s'=>['KLRW026900','KLRW028034'],
               'img_url'=>$wap_site_url."rotating_crown.jpg",
           ],

           //以下五款普通女戒的详情页放成了天生一对详情页,应该放香榭巴黎详情页
           [
               'style_sn_s'=>['KLRW030394','KLRW030393','KLRW030392','KLRW030395','KLRW030402'],
               'img_url'=>$wap_site_url."shannon_paris.jpg",
           ],

           //此款为心之吻系列,应该放心之吻详情页
           [
               'style_sn_s'=>['KLRW032781'],
               'img_url'=>$wap_site_url."kiss_heart.jpg",
           ],

           //此款为天使之翼系列，应该放天使之翼详情页
           [
               'style_sn_s'=>['KLRW026628','KLRM026627'],
               'img_url'=>$wap_site_url."angel_wings.jpg",
           ],

       ),

        //系列及款式分类
        'xilie_and_cat'=>array(
            //天鹅湖 女戒
            [
                'xilie'=>'天鹅湖',
                'cat_type_name'=>'女戒',
                'img_url'=>$wap_site_url."swan_lake.jpg",
            ],

             //天使之翼 女戒
            [
            'xilie'=>'天使之翼',
            'cat_type_name'=>'女戒',
            'img_url'=>$wap_site_url."angel_wings.jpg",
            ],

            //心之吻 女戒
            [
                'xilie'=>'心之吻',
                'cat_type_name'=>'女戒',
                'img_url'=>$wap_site_url."kiss_heart.jpg",
            ],
        ),

        //单独系列或者单独款式分类；优先系列
        'xilie_or_cat'=>array(
            [
                'name'=>'吊坠',
                'img_url'=>$wap_site_url."pendant.jpg",
            ],
            [
                'name'=>'耳饰',
                'img_url'=>$wap_site_url."earring.jpg",
            ],
            [
                'name'=>'手链',
                'img_url'=>$wap_site_url."bracelet.jpg",
            ],
            [
                'name'=>'天生一对',
                'img_url'=>$wap_site_url."tsyd.jpg",
            ],
            [
                'name'=>'香邂巴黎',
                'img_url'=>$wap_site_url."shannon_paris.jpg",
            ],
            [
                'name'=>'皇室公主',
                'img_url'=>$wap_site_url."huangshigongzhu.jpg",
            ],

        ),

      //前面没有匹配到的满足什么条件
      'other'=>array(
          [
              'key'=>'cat_type_name',
              'name'=>'女戒',
              'img_url'=>$wap_site_url."shannon_paris.jpg",
          ],
          [
              'key'=>'cat_type_name',
              'name'=>'情侣戒',
              'img_url'=>$wap_site_url."tsyd.jpg",
          ],
          [
              'key'=>'cat_type_name',
              'name'=>'项链',
              'img_url'=>$wap_site_url."pendant.jpg",
          ],


      ),



    );
return $config;
