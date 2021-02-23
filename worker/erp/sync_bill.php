<?php
/**
 *  同步与kela相关相关单据到erp中
 * @param unknown $msg
 */

function on_sync_bill($msg) {
    //创建类: 维修调拨到kela、批发性质的销售退货到kela
    //更新类: 总部p单门店签收、总部wf单门店签收，将状态同步到kela
    
    $bill_id = $msg['bill_id'];
    $bill_no = $msg['bill_no'];
    $msgId = isset($msg['msgId']) ? $msg['msgId']:0;
    
    $create_scope = $bill_no[0] == 'D' || ($bill_no[0] == 'M' && !isset($msg['ishop_sign']));
    $update_scope = $create_scope ? false : $bill_no[0] == 'S' || ($bill_no[0] == 'M' && isset($msg['ishop_sign']) && $msg['ishop_sign'] == 1);
    
    if (!$create_scope && !$update_scope) {
        return true;
    }
    
    echo PHP_EOL.'----------start to process ishop bill: '.$bill_no.PHP_EOL;
    
    $resp = false;
    if ($create_scope) {
        $resp = _create_bill($bill_id, $bill_no, $msgId);
    } else {
        $resp = _update_bill($bill_id, $bill_no, $msgId);
    }
    
    if ($resp === true) {
        try {
            echo '----try to notify ishop since we synced the bill.'.PHP_EOL;
            
            // 通知前端系统，单据同步完成
            global $worker;
            $worker->dispatch('ishop', array('event' => 'bill_synced', 'bill_no' => $bill_no, 'bill_id' => $bill_id, 'date' => date('Y-m-d H:i:s')));
            
        } catch (Exception $e) {
            echo $e->getMessage();
            loginfo($msgId, $bill_no, $e->getMessage());
        }
    }
    
    return $resp;
}

function loginfo($msgId, $bill_no, $err) {
    file_put_contents(__DIR__.'/'.date('Y-m-d').'_erp.sync_bill.err', json_encode(['msgId' => $msgId, 'err' =>$err, 'bill_no'=> $bill_no], JSON_UNESCAPED_UNICODE).PHP_EOL, FILE_APPEND);
}

function _create_bill($bill_id, $bill_no, $msgId) {
    echo '-----start create bill in erp for '.$bill_no.PHP_EOL;

    global $db_config;
    $ishop_db = new MysqlDB($db_config['ishop']);
    
    $bill = $ishop_db->getRow("select * from erp_bill where bill_id = {$bill_id}");
    if (empty($bill)) {
        loginfo($msgId, $bill_no, 'ishop找不到该单据');
        return -1;
    }
    
    /*
     * 1. D 单状态明细审核后才同步；
     * 2. M 单审核后才同步；。
     */
    if (!(($bill['bill_status'] == 2 && $bill['bill_type'] == 'M') || ($bill['bill_type'] == 'D' && $bill['item_status'] == 1 && $bill['bill_status'] == 1))) {
        // 单据必须是有效状态
        loginfo($msgId, $bill_no, '单据状态不符合要求，当前为'.$bill['bill_status']);
        return -1;
    }

    $to_company = $ishop_db->getRow("select * from company where id=".$bill['to_company_id']);
    if (!($to_company['id'] == 58 || $to_company['company_type'] == '4')) {
        // 入库公司必须是总部公司
        loginfo($msgId, $bill_no, '该单据不是去总部，当前为'.$bill['to_company_id']);
        return -1;
    }

    $target_type = '';
    $erp_bill_no = '';

    if ($bill['bill_type'] == 'D' && $bill['item_type'] == 'PF') {
        $target_type = 'H';
        $erp_bill_no = 'H'.substr($bill_no, 1);
    } else if ($bill['bill_type'] == 'M' && $bill['item_type'] == 'WX') {
        $target_type = 'WF';
        $erp_bill_no = 'WF'.substr($bill_no, 1);
    }
    
    if (empty($target_type)) {
        // 仅维修调拨单、批发销售退货单 需要同步到kela
        loginfo($msgId, $bill_no, '该单据无法转成目标单据');
        return -1;
    }
    
    // 理论上，erp不应该存在同单号的单据，但需要核实    
    $erp_db = new MysqlDB($db_config['zhanting']);
    $erp_db->selectDB('warehouse_shipping');

    $exist = $erp_db->getOne("select 1 from warehouse_bill where bill_no = '{$erp_bill_no}'");
    if ($exist) {
        echo PHP_EOL.'---------- the bill_no exits in warehouse_bill.'.PHP_EOL;
        loginfo($msgId, $bill_no, 'erp中已存在'.$erp_bill_no.'单据');

        return -1;
    }
    
    $goods = $ishop_db->getAll("select * from erp_bill_goods b inner join goods_items g on g.goods_id = b.goods_itemid where bill_id = {$bill_id}");
    if (empty($goods)) {
        loginfo($msgId, $bill_no, 'erp_bill_goods 找不到该单据的明细');

        return false;
    }
    
    $company_name = $ishop_db->getOne("select company_name from company where id={$bill['from_company_id']}");
    
    // 构建warehouse_bill对象
    $erp_bill = [
        'bill_no' => $erp_bill_no,
        'bill_type' => $target_type,
        'bill_status' => 1, // 默认到总部是'保存'状态
        'goods_num'   => $bill['goods_num'],
        'from_company_id' => $bill['from_company_id'],
        'from_company_name' => $company_name,
        'to_company_id' => $bill['to_company_id'],
        'to_company_name' => $to_company['company_name'],
        'bill_note' => $bill['remark'],
        'create_user' => $bill['check_user'],
        'create_time' => $bill['check_time'],
        'send_goods_sn' => $bill['express_sn'],
        'company_from'  => 'ishop' //特别标示
    ];
    
    if ($target_type == 'WF') {
        $erp_bill['to_warehouse_id'] = 606; //TODO
        $erp_bill['to_warehouse_name'] = '总公司维修库'; //TODO
        $erp_bill['fin_check_status'] = 1;
        $erp_bill['order_sn'] = $bill['order_sn'];
    } else if ($target_type == 'H') {
        $erp_bill['shijia'] = $bill['goods_total']; //批发总金额
        $erp_bill['pifajia'] = $bill['goods_total']; //批发总金额
        $erp_bill['goods_total'] = $bill['chengben_total']; //总成本
        $erp_bill['to_customer_id'] = $bill['wholesale_id']; //客户

        //TODO: 签收时选仓库
        //$erp_bill['to_warehouse_id'] = 1838; //TODO
        //$erp_bill['to_warehouse_name'] = '浩鹏后库'; //TODO
    }
    
    // 构建warehouse_bill_goods对象
    $erp_bill_goods = [];
    foreach ($goods as $g) {
        $item = [
            'bill_id' => 0, //后续覆写
            'bill_no' => $erp_bill_no,
            'bill_type' => $target_type,
            'goods_id'  => $g['goods_itemid'],
            'goods_sn'  => $g['goods_sn'],
            'goods_name' => $g['goods_name'],
            'num' => $g['goods_count'],
            'jinzhong' => $g['jinzhong'],
            'jingdu'  => $g['jingdu'],
            'yanse'  => $g['yanse'],
            'zhengshuhao' => $g['zhengshuhao'],
            'addtime' => $bill['create_time'],
            'pandian_guiwei' => '0-00-0-0',
            'zuanshidaxiao' => $g['zuanshidaxiao']
        ];
        
        if ($target_type == 'WF') {
            
        } else if ($target_type == 'H') {
            $yuanshichengbenjia = $erp_db->getOne("select yuanshichengbenjia from warehouse_goods where goods_id = '".$g['goods_itemid']."'");
            $item['pifajia'] = $g['sale_price'];    // 批发价 TODO: 是否需要扣除管理费？
            $item['sale_price'] = $yuanshichengbenjia; // 采购价
            $item['shijia'] = $g['sale_price'];     // 实价
        }
        
        $erp_bill_goods[] = $item;
    }
    
    // 开启事务处理
    try {
        $erp_db->beginTransaction();
        
        //保存单据
        $resp = $erp_db->insert('warehouse_bill', $erp_bill);
        if ($resp === false) {
            loginfo($msgId, $bill_no, ['err' => 'warehouse_bill 插入失败', 'bill' => $erp_bill]);
            throw new Exception('warehouse_bill 插入失败');
        }
        
        $erp_bill_id = $erp_db->insertId();
        $erp_bill_goods = array_map(function($v) use($erp_bill_id) {
            $v['bill_id'] = $erp_bill_id;
            return $v;
        }, $erp_bill_goods);
            
        //保存单据明细
        $resp = $erp_db->autoExecALL($erp_bill_goods, 'warehouse_bill_goods');
        if ($resp === false) {
            loginfo($msgId, $bill_no, ['err' => 'warehouse_bill_goods 插入失败', 'bill' => $erp_bill_goods]);
            throw new Exception('warehouse_bill_goods 插入失败');
        }
        
        // 修改货品状态
        $str_goods_id = implode(',', array_column($erp_bill_goods, 'goods_id'));
        
        if ($target_type == 'H') {
            $sql =" UPDATE `warehouse_goods` SET `is_on_sale`=11,kela_order_sn=null,order_goods_id=0 WHERE `goods_id` in (".$str_goods_id.") and `is_on_sale`in (3,2)"; //参考erp逻辑
        } else if ($target_type == 'WF') {
            $sql = "UPDATE `warehouse_goods` SET `weixiu_status` = 6, `weixiu_company_id` = 58, `is_on_sale` = 3 WHERE `goods_id` in (".$str_goods_id.");"; // 参考erp逻辑
            //$sql = "UPDATE `warehouse_goods` SET `weixiu_status` = 6 WHERE `goods_id` in (".$str_goods_id.");"; // 参考erp逻辑
        }
        
        $resp = $erp_db->exec($sql);
        if ($resp === false) {
            loginfo($msgId, $bill_no, ['err' => 'warehouse_goods 更新状态失败', 'goods_id' => $str_goods_id]);
            throw new Exception('warehouse_goods 更新状态失败');
        }
        
        $erp_db->commit();
        echo PHP_EOL.'----------Congratulations! '.$bill_no.' has been synced to erp.'.PHP_EOL;
        
        return true;  
    } catch(Exception $e) {
        echo $e->getMessage().PHP_EOL;
        $erp_db->rollback();

        loginfo($msgId, $bill_no, $e->getMessage());
        return false;
    }
}

function _update_bill($bill_id, $bill_no, $msgId) {
    
    echo '-----start update bill in erp for '.$bill_no.PHP_EOL;

    global $db_config;
    $ishop_db = new MysqlDB($db_config['ishop']);
    
    $bill = $ishop_db->getRow("select * from erp_bill where bill_id = {$bill_id}");
    if (empty($bill)) {
        loginfo($msgId, $bill_no, 'erp_bill找不到该单据');
        return -1;
    }
    
    $is_pf = $bill['bill_type'] == 'S' && $bill['item_type'] == 'PF';
    $is_m = $is_pf ? false : $bill['bill_type'] == 'M' && $bill['item_type'] == 'WX';
    
    if ($bill['bill_status'] != 4) {
        loginfo($msgId, $bill_no, '该单据状态不符合要求，当前为'.$bill['bill_status']);
        return -1;
    }

    $from_company = $ishop_db->getRow("select * from company where id=".$bill['from_company_id']);
    if (!($from_company['id'] == 58 || $from_company['company_type'] == 4)) {
        // 出库公司必须是kela
        loginfo($msgId, $bill_no, '该单据来源不是总部，当前为'.$bill['from_company_id']);
        return -1;
    }

    $erp_bill_no = '';
    if ($is_pf) {
        $erp_bill_no = 'P'.substr($bill_no, 1);
    } else if ($is_m) {
        $erp_bill_no = 'WF'.substr($bill_no, 1);
    }
    
    $erp_db = new MysqlDB($db_config['zhanting']);
    $erp_db->selectDB('warehouse_shipping');
    
    $erp_bill = $erp_db->getRow("select * from warehouse_bill where bill_no='{$erp_bill_no}';");
    if (empty($erp_bill)) {
        loginfo($msgId, $bill_no, 'erp找不到该单据');
        return false;
    }
    
    if ($is_pf) {
        
        if (in_array($erp_bill['bill_status'], [3,4])) return true;
        if ($erp_bill['bill_status'] == 1) {
            loginfo($msgId, $bill_no, '该单据状态不符合要求，当前为'.$erp_bill['bill_status']);
            return -1;
        }
    } else {
        if (in_array($erp_bill['bill_status'], [3,4])) return true;
    }
    
    $warehouse_name = $erp_db->getOne("select name from warehouse where id={$bill['to_house_id']}");
    // 执行签收操作
    try {
        $erp_db->beginTransaction();
        
        // 更新单据明细货品状态
        if ($is_pf) {
            // 忽略经销商成本价的计算，该字段在P单审核时已经处理。
            //$resp = $erp_db->exec("update warehouse_goods g inner join warehouse_bill_goods b on b.goods_id = g.goods_id set g.is_on_sale = 2, g.warehouse_id = '{$bill['to_house_id']}', g.warehouse = '{$warehouse_name}' where b.bill_id = '{$erp_bill['id']}';");
            // 目前来说，批发货品一旦总部审核，对总部而言，货品状态是已销售，以下设置is_on_sale=3是为确保
            $resp = $erp_db->exec("update warehouse_goods g inner join warehouse_bill_goods b on b.goods_id = g.goods_id set g.is_on_sale=3,g.company='".$from_company['company_name']."',g.company_id=".$bill['from_company_id'].",g.warehouse='',g.warehouse_id=0 where b.bill_id = '{$erp_bill['id']}';"); // 确保状态和公司信息
        } else {
            // 目前来说，批发货品一旦总部审核，对总部而言，货品状态是已销售，以下设置is_on_sale=3是为确保
            $resp = $erp_db->exec("update warehouse_goods g inner join warehouse_bill_goods b on b.goods_id = g.goods_id set g.is_on_sale = 3, g.weixiu_status = 4, g.weixiu_company_id = 0, g.weixiu_company_name ='', g.weixiu_warehouse_id=0, g.weixiu_warehouse_name='' where b.bill_id = '{$erp_bill['id']}';");
            //$resp = $erp_db->exec("update warehouse_goods g inner join warehouse_bill_goods b on b.goods_id = g.goods_id set g.weixiu_status = 4, g.weixiu_company_id = 0, g.weixiu_company_name ='', g.weixiu_warehouse_id=0, g.weixiu_warehouse_name='' where b.bill_id = '{$erp_bill['id']}';");
        }
        
        if ($resp === false){
            loginfo($msgId, $bill_no, '更新warehouse_goods失败');
            throw new Exception('更新warehouse_goods失败');
        }
        
        // 更新单据状态
        if ($is_pf) {
            $resp = $erp_db->autoExec([
                'bill_status' => 4,
                'to_warehouse_id'  => $bill['to_house_id'],
                'to_warehouse_name' => $warehouse_name,
                'sign_user' => $bill['sign_user'],
                'sign_time' => $bill['sign_time']
            ], 'warehouse_bill', 'UPDATE', ['id' => $erp_bill['id']]);
        } else {
            $resp = $erp_db->autoExec([
                'bill_status' => 4,
                'check_user' => $bill['check_user'],
                'check_time' => $bill['check_time']
            ], 'warehouse_bill', 'UPDATE', ['id' => $erp_bill['id']]);
        }            
        
        if ($resp === false) {
            loginfo($msgId, $bill_no, '更新warehouse_bill失败');
            throw new Exception('更新warehouse_bill失败');
        }
        
        $erp_db->commit();
        echo PHP_EOL.'--------finish update bill '.$erp_bill_no.' in erp'.PHP_EOL;         
       
    } catch (Exception $e) {
        echo $e->getMessage().PHP_EOL;
        $erp_db->rollback();

        loginfo($msgId, $bill_no, $e->getMessage());
        return false;
    }

    if ($is_pf) {
        // 如果是批发签收，触发订单数据拉取逻辑
        try {
            $orders = $ishop_db->getAll(
            "SELECT DISTINCT o.order_sn, o.order_id from goods_items g inner join erp_bill_goods b on b.goods_itemid= g.goods_id
            inner join order_goods d on d.rec_id = g.order_detail_id
            inner join orders o on d.order_id = o.order_id
            where b.bill_id = {$bill_id};");

            if (!empty($orders)) {
                // 通知前端系统，单据同步完成
                echo PHP_EOL .'------------try to notify order for these'.PHP_EOL;
                global $worker;
                foreach ($orders as $ord) {
                
                    $worker->dispatch('ishop', array('event' => 'pull_order', 'order_id' => $ord['order_id'], 'order_sn' => $ord['order_sn'], 'source' => 'erp.sync_bill'));
            
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage().PHP_EOL;
            loginfo($msgId, $bill_no, $e->getMessage());
        }
    }
    
    return true;
}

function contains($str, $substr) {
    return strpos($str, $substr) !== false;
}
