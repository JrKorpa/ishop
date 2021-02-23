<?php 

/**
 * 从erp中同步单据及相关信息到ishop
 * @param unknown $msg
 */
function on_sync_bill($msg) {
    //创建类: 批发单P、维修调拨WF;
    //更新类: 批发退货单总部审核、维修调拨单总部审核，状态会从erp同步到门店
    $bill_id = $msg['bill_id'];
    $bill_no = $msg['bill_no'];
    $msgId = isset($msg['msgId']) ? $msg['msgId'] : 0;
    
    $create_scope = $bill_no[0] == 'P' || (strpos($bill_no, 'WF') === 0 && !isset($msg['erp_checked']));
    $update_scope = $create_scope ? false : $bill_no[0] == 'H' || (strpos($bill_no, 'WF') === 0 && isset($msg['erp_checked']) && $msg['erp_checked'] == 1);
           
    if (!$create_scope && !$update_scope) {
        return true;
    }
    
    echo PHP_EOL.'----------start to process erp bill: '.$bill_no.PHP_EOL;
    
    $resp = false;
    if ($create_scope) {
        $resp = _create_bill($bill_id, $bill_no, $msgId);
    } else {
        $resp = _update_bill($bill_id, $bill_no, $msg);
    }
    
    if ($resp === true) {
        try {
            echo '----try to notify ERP since we synced the bill.'.PHP_EOL;
            
            // 通知后端系统，单据同步完成
            global $worker;
            $worker->dispatch('erp', array('event' => 'bill_synced', 'bill_no' => $bill_no, 'bill_id' => $bill_id, 'date' => date('Y-m-d H:i:s')));
            
        } catch (Exception $e) {
            echo $e->getMessage();
            loginfo($msgId, $bill_no, $e->getMessage());
        }
    }
    
    return $resp;
}

function loginfo($msgId, $bill_no, $err) {
    file_put_contents(__DIR__.'/'.date('Y-m-d').'_ishop.sync_bill.err', json_encode(['msgId' =>$msgId, 'err' =>$err, 'bill_no'=>$bill_no], JSON_UNESCAPED_UNICODE).PHP_EOL, FILE_APPEND);
}

function _create_bill($bill_id, $bill_no, $msgId) {
    echo '-----start create bill in ishop for '.$bill_no.PHP_EOL;

    global $setting_config;
    $erp_db = new MysqlDB($setting_config['zhanting']);
    $erp_db->selectDB('warehouse_shipping');
    
    $bill = $erp_db->getRow("select * from warehouse_bill where id={$bill_id}");
    if (empty($bill)) {
        loginfo($msgId, $bill_no, 'erp找不到该单据');
        return -1;
    }
    
    /*
     * 1. P 单审核后才同步；
     * 2. WF 单总部一创建就要同步，目前总部没有做审核，由入库方审核。
     */
    $bill_status_checked = ($bill_no[0] == 'P' && $bill['bill_status'] == 2) || (strpos($bill_no, 'WF') === 0 && $bill['bill_status'] == 2); //稍微放开点
    if (!$bill_status_checked) {
        loginfo($msgId, $bill_no, 'erp中该单据状态不符合要求，当前为'.$bill['bill_status']);
        return -1;
    }

    $ishop_db = new MysqlDB($setting_config['ishop']);
    $from_company = $ishop_db->getRow("select * from company where id=".$bill['from_company_id']);
    if (!($from_company['id'] == 58 || $from_company['company_type'] == 4)) {
        // 出库公司必须是kela
        loginfo($msgId, $bill_no, '该单据来源不是总部，当前为'.$bill['from_company_id']);
        return -1;
    }

    //转成对应单号
    $ishop_bill_no = '';
    if ($bill_no[0] == 'P') {
        $ishop_bill_no = 'S'.substr($bill_no, 1);
    } else if (strpos($bill_no, 'WF') === 0) {
        $ishop_bill_no = 'M'.substr($bill_no, 2);
    }
    
    // 理论上，ishop不应该存在同单号的单据，但需要核实
    $exist = $ishop_db->getOne("select 1 from erp_bill where bill_no = '{$ishop_bill_no}'");
    if ($exist) {
        echo PHP_EOL.'---------- the bill_no exits in erp_bill.'.PHP_EOL;
        loginfo($msgId, $bill_no, 'ishop中已存在'.$ishop_bill_no.'单据');
        return -1;
    }
    
    $ishop_bill = [
        'bill_no' => $ishop_bill_no,
        'bill_status' => 2, //默认总部已审核
        'from_company_id' => $bill['from_company_id'],
        'to_company_id' => $bill['to_company_id'],
        'remark' => $bill['bill_note'],
        'create_user' => $bill['create_user'],
        'create_time' => $bill['create_time'],
        'check_user' => $bill['check_user'],
        'check_time' => $bill['check_time'],
        'goods_num' => $bill['goods_num'],
        'express_sn' => $bill['send_goods_sn'],
        'order_sn' => $bill['order_sn']
    ];
    
    if ($bill['bill_type'] == 'P') {
        $ishop_bill['bill_type'] = 'S';
        $ishop_bill['item_type'] = 'PF';
        $ishop_bill['supplier_id'] = $bill['from_company_id'];
        $ishop_bill['out_warehouse_type'] = $bill['out_warehouse_type'];
        $ishop_bill['in_warehouse_type'] = $bill['out_warehouse_type'];
        
        switch ($bill['p_type']){
            case '经销商备货':
                $ishop_bill['pifa_type'] = 1;break;
            case '经销商客单':
                $ishop_bill['pifa_type'] = 2;break;
            case '新店首批':
                $ishop_bill['pifa_type'] = 3;break;
            case '指定外采':
                $ishop_bill['pifa_type'] = 4;break;
            case '附加销售':
                $ishop_bill['pifa_type'] = 5;break;
            case '联营备货':
                $ishop_bill['pifa_type'] = 6;break;
            case '联营客单':
                $ishop_bill['pifa_type'] = 7;break;
            case '散客内购':
                $ishop_bill['pifa_type'] = 8;break;
            case '天生一对':
                $ishop_bill['pifa_type'] = 9;break;
        }
        
        $ishop_bill['wholesale_id'] = $bill['to_customer_id'];
        $ishop_bill['chengben_total'] = 0; // 根据货品明细累加
        $ishop_bill['goods_total'] = 0; // 根据货品明细累加
        // 既然货品是总公司的，相关成本字段不需要存储在经销商数据中，忽略！
        
    } else if ($bill['bill_type'] == 'WF') {
        $ishop_bill['bill_type'] = 'M';
        $ishop_bill['item_type'] = 'WX';
        //$ishop_bill['to_house_id'] = $bill['to_warehouse_id'];
    }
    
    $goods = $erp_db->getAll("select * from warehouse_bill_goods where bill_id={$bill_id}");
    if (empty($goods)) {
        loginfo($msgId, $bill_no, 'warehouse_bill_goods 找不到该单据的明细');
        return false;
    }
    
    $shop_bill_items = [];
    foreach ($goods as $g) {
        
        $item = [
            'bill_id' => 0, //后续覆写
            'bill_no' => $ishop_bill_no,
            'bill_type' => $ishop_bill['bill_type'],
            'goods_itemid'  => $g['goods_id'],
            'goods_sn'  => $g['goods_sn'],
            'goods_name' => $g['goods_name'],
            'goods_count' => $g['num'] ==  0 ? 1 : $g['num'],
            'jinzhong' => $g['jinzhong'],
            'jingdu'  => $g['jingdu'],
            'yanse'  => $g['yanse'],
            'zhengshuhao' => $g['zhengshuhao'],
            'zuanshidaxiao' => $g['zuanshidaxiao'],
        ];
        
        if ($ishop_bill['bill_type'] == 'M') {
            
        } else if ($ishop_bill['bill_type'] == 'S') {
            $item['yuanshichengben'] = 0; // 忽略 货品在总部的采购价
            $item['mingyichengben'] = 0; // 忽略 货品在总部的名义价
            $item['jijiachengben'] = 0; // 忽略 货品在总部的计价成本
            $item['sale_price'] = $g['shijia'] + $g['management_fee'];     // 批发价 + 管理费 =》 售价
            $item['management_fee'] = $g['management_fee']; //管理费
        }

        $ishop_bill['chengben_total'] += $g['shijia']; 
        $ishop_bill['goods_total'] += $item['sale_price']; // 货品金额包含管理费
        
        $shop_bill_items[] = $item;
    }
    
    $orders_to_refresh = [];

    // 开启事务
    try {
        $ishop_db->beginTransaction();
        
        // 创建单据
        echo 'try to insert erp_bill '.$bill_no. PHP_EOL;
        $resp = $ishop_db->insert('erp_bill', $ishop_bill);
        if ($resp === false) {
            loginfo($msgId, $bill_no, ['err' => 'erp_bill 插入失败', 'bill' => $ishop_bill]);
            throw new Exception('erp_bill 插入失败');
        }
        
        $ishop_bill_id = $ishop_db->insertId();
        $shop_bill_items = array_map(function($v) use($ishop_bill_id) {
            $v['bill_id'] = $ishop_bill_id;
            return $v;
        }, $shop_bill_items);
            
        // 保存单据明细
        echo 'try to insert erp_bill_goods for bill '.$bill_no. PHP_EOL;
        $resp = $ishop_db->autoExecALL($shop_bill_items, 'erp_bill_goods');
        if ($resp === false) {
            loginfo($msgId, $bill_no, ['err' => 'erp_bill_goods 插入失败', 'bill_goods' => $shop_bill_items]);           
            throw new Exception('erp_bill_goods 插入失败');
        }
        
        /* 调整货品状态
         * 1. 维修调拨，货品的维修状态调整为 转仓中； 货品库存状态调整为 调拨中
         * 2. P单，新增货品, 货品状态为 收货中；
         */
        $str_goods_id = implode(',', array_column($shop_bill_items, 'goods_itemid'));
        if ($ishop_bill['bill_type'] == 'S') {
            $put_in_type = '';
            if ($bill['out_warehouse_type'] == '1') {
                $put_in_type = 1; //购买
            } else if ($bill['out_warehouse_type'] == '2') {
                $put_in_type = 4; //借入
            }
            
            $goods = $erp_db->getAll("select * from warehouse_goods WHERE `goods_id` in (".$str_goods_id.") ");

            $app_order_db = new MysqlDB($setting_config['zhanting']);
            $app_order_db->selectDB('app_order');
            
            foreach ($goods as &$g) {
                unset($g['id']);
                unset($g['update_time']);
                
                $g['product_type'] = $g['product_type1'];
                unset($g['product_type1']);
                
                $g['cat_type'] = $g['cat_type1'];
                unset($g['cat_type1']);
                
                $g['order_sn'] = $g['kela_order_sn'];
                unset($g['kela_order_sn']);
                
                if (empty($g['order_goods_id'])) {
                    $g['order_detail_id'] = 0;
                } else {
                    // 获取订单 ds_xiangci
                    $g['order_detail_id'] = 0;
                    $g['order_sn'] = '';
                    
                    $order = $app_order_db->getRow("SELECT ifnull(ds_xiangci, 0) as ds_xiangci, i.order_sn from app_order_details d inner join base_order_info i on i.id = d.order_id where d.id =".$g['order_goods_id']);
                    if (!empty($order)) {
                        // 如果商品已退 或 订单商品已换货，此处不需要再绑定到之前的订单
                        $order_goods = $ishop_db->getRow("select is_return, goods_itemid, order_id from order_goods where rec_id='".$order['ds_xiangci']."'");
                        if (!empty($order_goods) && $order_goods['is_return'] != 1 && $order_goods['goods_itemid'] == $g['goods_id']) {
                            $g['order_detail_id'] = $order['ds_xiangci'];
                            $g['order_sn'] = $order['order_sn'];
                        } else {
                            // 查询不到订单??? 此处为确保，订单号继续保留，明细id暂不绑定
                            if (!empty($order_goods)) $g['order_sn'] = $order['order_sn'];
                        }

                        if (!empty($g['order_sn'])) {
                            $orders_to_refresh[$g['order_sn']] = empty($order_goods) ? 0 : $order_goods['order_id'];
                        }
                    }
                }
                unset($g['order_goods_id']);
                
                // caizhi  jinse
                list($caizhi, $jinse) = split_caizhi($g['caizhi']);
                $g['caizhi'] = $caizhi;
                $g['jinse'] = $jinse;
                
                $g['peijianjinzhong'] = $g['peijianjinchong'];
                unset($g['peijianjinchong']);
                
                $g['is_hrds'] = $g['xinyaozhanshi'];
                unset($g['xinyaozhanshi']);
                
                $g['is_on_sale'] = 1; //ensure
                $g['company_id'] = $bill['to_company_id']; //ensure   
                $g['prc_id'] = $bill['from_company_id']; // 供应商调整为总公司
                $g['prc_name'] = $bill['from_company_name']; // 供应商调整为总公司
                unset($g['company']); // 清空公司名称
                $g['warehouse_id'] = 0; // 清空仓库
                unset($g['warehouse']); // 清空仓库
                unset($g['weixiu_status']); // 清空维修相关
                unset($g['weixiu_company_id']); //清空维修相关
                unset($g['weixiu_company_name']); // 清空维修相关
                unset($g['weixiu_warehouse_id']); // 清空维修相关
                unset($g['weixiu_warehouse_name']); // 清空维修相关
                unset($g['chuku_time']); // 清空出库时间
                
                $g['addtime'] = date("Y-m-d h:i:s"); // override
                
                // 调整货品的成本、入库方式
                foreach ($shop_bill_items as $k=>$item) {
                    if ($item['goods_itemid'] == $g['goods_id']) {
                        
                        $g['yuanshichengbenjia'] = $item['sale_price'] - $item['management_fee'];
                        $g['mingyichengben'] = $g['yuanshichengbenjia'];
                        $g['jijiachengben'] = $item['sale_price'];
                        $g['management_fee'] = $item['management_fee'];
                        $g['put_in_type'] = $put_in_type;
                        
                        unset($shop_bill_items[$k]);
                        break;
                    }
                }
            }
            //更新or插入数据
            echo 'try to upsert goods_items for bill '.$bill_no. PHP_EOL;
            $resp = $ishop_db->upsert($goods, 'goods_items', ['goods_id']);
            if ($resp === false) {
                loginfo($msgId, $bill_no, ['err' => 'goods_items 插入失败', 'goods' => $goods]);
                throw new Exception('goods_items 插入失败');
            }
            
        } else if ($ishop_bill['bill_type'] == 'M') {
            $sql = "update goods_items set weixiu_status=6 where goods_id in (".$str_goods_id.") ";
            echo 'try to upsert goods_items for bill '.$bill_no. PHP_EOL;
            $resp = $ishop_db->exec($sql);
            if ($resp === false) {
                loginfo($msgId, $bill_no, ['err' => 'goods_items 更新维修状态失败', 'goods_id' => $str_goods_id]);
                throw new Exception('goods_items 更新维修状态失败');
            }
        }
        
        // 添加单据日志
        echo 'try to insert erp_bill_log for bill '.$bill_no. PHP_EOL;
        $resp = $ishop_db->insert('erp_bill_log', ['bill_id'=> $ishop_bill_id, 'bill_status' => $ishop_bill['bill_status'], 'remark' => '总部下发单据'.$bill_no, 'create_user' => $ishop_bill['check_user'], 'create_time' => $ishop_bill['check_time']]);
        if ($resp === false) {
            loginfo($msgId, $bill_no, 'erp_bill_log 插入失败');
            throw new Exception('erp_bill_log 插入失败');            
        }
        
        $ishop_db->commit();
        echo PHP_EOL.'----------Congratulations! '.$bill_no.' has been synced to ishop.'.PHP_EOL;
    } catch(Exception $e) {
        echo $e->getMessage().PHP_EOL;
        $ishop_db->rollback();
        
        loginfo($msgId, $bill_no, json_encode($e));
        return false;
    }

    if (!empty($orders_to_refresh)) {
        try {
            // 通知前端系统
            echo PHP_EOL .'------------try to notify order for these'.PHP_EOL;
            global $worker;
            foreach ($orders_to_refresh as $sn => $od_id) {
                $worker->dispatch('ishop', array('event' => 'pull_order', 'order_id' => $od_id, 'order_sn' => $sn, 'source' => 'ishop.sync_bill'));
            }
        } catch (Exception $e) {
            echo $e->getMessage().PHP_EOL;
            loginfo($msgId, $bill_no, $e->getMessage());
        }
    }

    return true;
}

function _update_bill($bill_id, $bill_no, $msg) {
    $msgId = isset($msg['msgId']) ? $msg['msgId'] : 0;
    echo '-----start update bill in ishop for '.$bill_no.PHP_EOL;

    global $setting_config;
    $erp_db = new MysqlDB($setting_config['zhanting']);
    $erp_db->selectDB('warehouse_shipping');
    
    $bill = $erp_db->getRow("select * from warehouse_bill where id={$bill_id}");
    if (empty($bill)) {
        loginfo($msgId, $bill_no, 'erp找不到该单据');
        return -1;
    }
    
    $ishop_db = new MysqlDB($setting_config['ishop']);
    $to_company = $ishop_db->getRow("select * from company where id=".$bill['to_company_id']);
    if (!($to_company['id'] == 58 || $to_company['company_type'] == '4')) {
        // 入库公司必须是总部公司
        loginfo($msgId, $bill_no, '该单据不是去总部，当前为'.$bill['to_company_id']);
        return -1;
    }
    
    $is_h = $bill_no[0] == 'H';
    $is_wf = $is_h ? false : strpos($bill_no, 'WF') === 0;
    
    if (!(($is_wf && $bill['bill_status'] == 2) || ($is_h && $bill['bill_status'] > 1))) {
        loginfo($msgId, $bill_no, 'erp中该单据状态不符合要求，当前为'.$bill['bill_status']);
        return -1;
    } 

    $ishop_bill_no = '';
    if ($is_h) {
        if (!isset($msg['erp_check_resp'])) {
            loginfo($msgId, $bill_no, 'erp中单据审核结果不清楚');
            return -1;
        }
        $ishop_bill_no = 'D'.substr($bill_no, 1);
    } else if ($is_wf) {
        $ishop_bill_no = 'M'.substr($bill_no, 2);
    }
    
    $ishop_bill = $ishop_db->getRow("select * from erp_bill where bill_no = '{$ishop_bill_no}'");
    
    if (empty($ishop_bill)) {
        loginfo($msgId, $bill_no, 'ishop 找不到该单据');
        return false;
    }

    if ($is_wf && $ishop_bill['bill_status'] == 4) {
        echo '----Skip update this bill since it was signed.'.PHP_EOL;
        loginfo($msgId, $bill_no, 'ishop 中该单据状态不符合要求, 当前是'.$ishop_bill['bill_status']);
        return true;
    }
    
    try {
        $ishop_db->beginTransaction();
        
        $bill_updata = [];
        $bill_log = ['bill_id'=> $ishop_bill['bill_id']];
        $sql = '';
        
        //更新单据货品
        if ($is_h) {
            $check_resp = $msg['erp_check_resp'];
            if ($check_resp == 2) {
                // 审核通过，总部货品收货中，门店单据状态调整为已审核，货品不调整;
                $bill_updata['bill_status'] = 2;
                $bill_updata['check_user'] = $bill['check_user'];
                $bill_updata['check_time'] = $bill['check_time'];
                $bill_updata['remark'] = $bill['bill_note'];
                
                $bill_log['bill_status'] = 2;
                $bill_log['remark'] = '总部已审核同意对应单据'.$bill_no;
                $bill_log['create_user'] = $bill['check_user'];
                $bill_log['create_time'] = $bill['check_time'];
                
            } else if ($check_resp == 3) {
                // 审核驳回，门店单据状态调整为取消，货品状态调整库存;
                $bill_updata['bill_status'] = 3;
                $bill_updata['check_user'] = $bill['check_user'];
                $bill_updata['check_time'] = $bill['check_time'];
                $bill_updata['remark'] = $bill['bill_note'];
                
                $bill_log['bill_status'] = 3;
                $bill_log['remark'] = '总部已驳回对应单据'.$bill_no;
                $bill_log['create_user'] = $bill['check_user'];
                $bill_log['create_time'] = $bill['check_time'];

                $sql = "update goods_items g INNER JOIN erp_bill_goods b on b.goods_itemid=g.goods_id set g.is_on_sale=2 where b.bill_id = {$ishop_bill['bill_id']} ";
                
            } else if ($check_resp == 4) {
                // 总部签收, 门店单据调整已签收，货品状态调整为已返厂;
                $bill_updata['bill_status'] = 4;
                $bill_updata['sign_user'] = $bill['sign_user'];
                $bill_updata['sign_time'] = $bill['sign_time'];
                
                $bill_log['bill_status'] = 4;
                $bill_log['remark'] = '总部已签收对应单据'.$bill_no;
                $bill_log['create_user'] = $bill['sign_user'];
                $bill_log['create_time'] = $bill['sign_time'];
                
                $sql = "update goods_items g INNER JOIN erp_bill_goods b on b.goods_itemid=g.goods_id set g.is_on_sale=9, g.company_id=0, g.company='', g.warehouse_id=0, g.warehouse='' where b.bill_id = {$ishop_bill['bill_id']} ";
               
            } else if ($check_resp == 5) {
                // 总部财务审核， 门店单据结价
                $bill_updata['is_settled'] = 1;
                $bill_updata['settle_user'] = $bill['fin_check_user'];
                $bill_updata['settle_time'] = $bill['fin_check_time']; 
                
                $bill_log['bill_status'] = 4;
                $bill_log['remark'] = '总部财务已审核对应单据'.$bill_no;
                $bill_log['create_user'] = $bill['fin_check_user'];
                $bill_log['create_time'] = $bill['fin_check_time'];               
            }
        } else {
            
            $bill_updata['bill_status'] = 4;
            $bill_updata['sign_user'] = $bill['sign_user'];
            $bill_updata['sign_time'] = $bill['sign_time'];
            
            $bill_log['bill_status'] = 4;
            $bill_log['remark'] = '总部已审核或签收对应单据'.$bill_no;
            $bill_log['create_user'] = $bill['sign_user'];
            $bill_log['create_time'] = $bill['sign_time'];
            
            $sql = "update goods_items g INNER JOIN erp_bill_goods b on b.goods_itemid=g.goods_id set g.weixiu_status=3,g.weixiu_company_id=58,g.weixiu_company_name='总公司' where b.bill_id = {$ishop_bill['bill_id']} ";
        }
        
        // 更新商品
        if (!empty($sql)) {
            $resp = $ishop_db->exec($sql);
            if ($resp === false) {
                loginfo($msgId, $bill_no, '更新goods_items失败');
                throw new Exception('更新goods_items失败');
            }
        }
        
        //更新单据
        $resp = $ishop_db->autoExec($bill_updata, 'erp_bill','UPDATE',['bill_id' => $ishop_bill['bill_id']]);        
        if ($resp === false) {
            loginfo($msgId, $bill_no, '更新erp_bill失败');
            throw new Exception('更新erp_bill失败');
        }
        
        // 添加单据日志
        $resp = $ishop_db->insert('erp_bill_log', $bill_log);
        if ($resp === false) {
            loginfo($msgId, $bill_no, 'erp_bill_log插入失败');
            throw new Exception('erp_bill_log插入失败');
        }
        
        $ishop_db->commit();
        echo PHP_EOL.'--------finish update bill '.$ishop_bill_no.' in ishop'.PHP_EOL;    
        
    } catch (Exception $e) {
        echo $e->getMessage().PHP_EOL;
        $ishop_db->rollback();

        loginfo($msgId, $bill_no, $e->getMessage());
        return false;
    }
    
    return true;   
}

function split_caizhi($caizhi) {
    $keys = ['玫瑰金', '玫瑰黄', '玫瑰白', '玫瑰红', '彩金', '分色', '黄白', '黄', '白', '红', '金', '紅','黃', '按图做'];
    foreach ($keys as $k) {
        if (strpos($caizhi, $k) !== false) {
            return [str_replace($k, '', $caizhi), $k];
        }
    }
    
    return [$caizhi,'无'];
}

function contains($str, $substr) {
    return strpos($str, $substr) !== false;
}

?>
