<?php

$facework_list = ['光面','磨砂','拉砂' ,'光面&磨砂', '光面&拉砂', '其它', '特殊', 'CNC工艺','勾丝'];
$xiang_qian_list = ['工工厂配钻，工厂镶嵌', '不需工厂镶嵌', '需工厂镶嵌', '客户先看钻再返厂镶嵌', '镶嵌4C裸钻', '镶嵌4C裸钻，客户先看钻','成品', '半成品'];
/*
 *  从ishop同步订单到erp
 */
function on_sync_order($msg) {

    $msgId = isset($msg['msgId']) ? $msg['msgId'] : 0;

	$data = $msg['data'];
	if (!isset($data['order_sn'])) return true;
	
	$order_sn = $data['order_sn'];

    global $db_config;
	$db = new MysqlDB($db_config['zhanting']);
    $db_front = new MysqlDB($db_config['zhanting_front']);
	
	$updating = false;
	$erp_order_status = 0;
	
	//1. 是否订单号已存在
	$order_info = $db->getRow("select * from app_order.base_order_info where order_sn='{$order_sn}'");
	if (!empty($order_info)) {

		$erp_order_status = $order_info['order_status'];

		// 存在订单号，并且是自己
		if($order_info['department_id'] == $data['store_id'] 
			/*&& $order_info['create_user'] == $data['seller_name']*/){
			// 订单如果已生产未出厂，则跳过同步
			if (in_array($order_info['buchan_status'], [2,3])) {
				echo 'skip update since order goods is making...'.PHP_EOL;
				log_order($msgId, $order_sn, '订单正在生产中');
				
				// 通知前端系统，订单同步完成
				global $worker;
				$worker->dispatch('ishop', array('event' => 'order_synced', 'order_sn' => $order_sn, 'order_id' => $data['order_id'], 'date' => date('Y-m-d H:i:s'), 'is_xianhuo' => $order_info['is_xianhuo'], 'order_state' => $data['order_state']));
				
			    return true;
			}
			
			$updating = true;
			
		} else {
			// 订单号重复
			log_order($msgId, $order_sn, '订单号重复');
			return -1;
		}
	} else if ($data['order_state'] < 5) {
		// 这种未审核订单可以不用同步
		//return true;
	}




	//1. 订单主表
	$order_info['order_sn'] = $data['order_sn'];
	$order_info['bespoke_id'] = is_numeric($data['bespoke_id']) ? intval($data['bespoke_id']) : 0;
	$order_info['user_id'] = is_numeric($data['buyer_id']) ? intval($data['buyer_id']) : 0;
	$order_info['consignee'] = $data['buyer_name'];
	$order_info['mobile'] = $data['buyer_phone'];

	$order_info['customer_source_id'] = empty($data['customer_source_id']) ? 2202 : $data['customer_source_id']; //默认自然进店
	 
	$order_info['department_id'] = $data['store_id'];
	$order_info['create_time'] = date('Y-m-d H:i:s', $data['add_time']);
	$order_info['modify_time'] = $data['update_time'];
	$order_info['create_user'] = $data['seller_name']; 
	$order_info['check_time'] = $data['audit_time'];
	$order_info['check_user'] = $data['audit_by'];
	$order_info['order_remark'] = $data['remark']; 
	$order_info['is_xianhuo'] = 10; // override after details loop;
	$order_info['is_zp'] = 10;       // override after details loop;
	
	$order_info['pay_date'] = '0000-00-00 00:00:00'; 
	if (!empty($data['payment_time'])) {
		$order_info['pay_date'] = date("Y-m-d H:i:s", $data['payment_time']);
	}

	$order_info['order_pay_status'] = $data['pay_status']; // override after details loop;

	if ($data['order_state'] == 0) {
		// 已取消
		$order_info['order_status'] = 3;
		$order_info['delivery_status'] = 1;
		$order_info['send_good_status'] = 1;
	} else if($data['order_state'] == 5){
		// 待确认
		$order_info['order_status'] = 1;
		$order_info['delivery_status'] = 1;
		$order_info['send_good_status'] = 1;
	} else if($data['order_state'] >= 10){
		// 待支付及后续
		$order_info['order_status'] = 2;
		// 订单处于待发货状态
		if ($data['order_state'] == 25) { 
			$order_info['delivery_status'] = 2; //允许配货
			$order_info['send_good_status'] = 1; //未发货
		} else if ($data['order_state'] >= 30) {
			$order_info['delivery_status'] = 5; //已配货
			$order_info['send_good_status'] = 2; //已发货
		} else {
			$order_info['delivery_status'] = 1; //未配货
			$order_info['send_good_status'] = 1; //未发货
		}
	}
	
	//2. 订单商品明细
	$goods = [];
	$goods_amount = 0; //商品总金额（折扣前）
	$zhekou_amount = 0; //商品折扣总额
	$qb_sn_list = []; // 起版号
	$zb_xianhuo_list = []; //总部现货列表
	if (!empty($data['extend_order_goods'])) {
	    
	    //global $facework_list, $xiang_qian_list;
	    
	    $existing_items = $updating ? $db->getAll("select * from app_order_details where order_id={$order_info['id']};") : [];
		foreach($data['extend_order_goods'] as $g) {
			
			$item_updating = false;
			$item = [];
			
			if ($updating) {
				foreach($existing_items as $key => $ei) {
					if ($ei['ds_xiangci'] == $g['rec_id']) {
						$item = $ei;
						$item_updating = true;
						unset($existing_items[$key]);
						
						break;
					} 
				}
			}
			
			$item['goods_sn'] = $g['style_sn'];

			$item['goods_name'] = $g['goods_name'];
			$item['goods_price'] = $g['goods_price'];
			
			$goods_amount += $g['goods_price'];  //商品总金额累加
			
			$item['favorable_price'] = $g['goods_price'] - $g['goods_pay_price'];
			$item['favorable_status'] = 3;
			
			$zhekou_amount += $item['favorable_price']; // 折扣总金额累加
						
			$item['goods_count'] = $g['goods_num'];		

			$item['is_stock_goods'] = $g['is_xianhuo'];
			if (intval($order_info['is_xianhuo']) > intval($g['is_xianhuo'])) {
				$order_info['is_xianhuo'] = $g['is_xianhuo']; //取较小的值
			}
			
			$item['is_return'] = $g['is_return'];
			$item['cart'] = $g['carat'];
			$item['cut'] = $g['cut'];			
			$item['clarity'] = $g['clarity'];
			$item['color'] = $g['color'];
			$item['cert'] = $g['cert_type'];
			$item['zhengshuhao'] = $g['cert_id'];
			$item['caizhi'] = $g['caizhi'];			
			$item['jinse'] = $g['jinse'];			
			$item['jinzhong'] = $g['jinzhong'];
			$item['zhiquan'] = $g['zhiquan'];					
			$item['kezi'] = resolve_kezi(isset($g['kezi_raw']) ? $g['kezi_raw'] : $g['kezi']);
			//$item['details_remark'] = '刻字内容：'.$item['kezi']; // 保留原始刻字内容 
			$item['face_work'] = empty($g['face_work']) ? '' : $g['face_work'];
			$item['xiangqian'] = empty($g['xiangqian']) ? '' : $g['xiangqian'];										

			$item['xiangkou'] = $g['xiangkou'];						
			$item['is_peishi'] = $g['peishi_type'];
			
			$item['is_zp'] = $g['goods_type'] == 5 ? 1 : 0;
			if (intval($order_info['is_zp']) > intval($item['is_zp'])) {
			    $order_info['is_zp'] = $item['is_zp']; //取较小的值
			}
			$item['is_finance'] = $g['is_finance'] == 1 ? 2 : 1;			

			$item['is_cpdz'] = $g['is_cpdz'];			
			$item['tuo_type'] = $g['tuo_type'] == 2 ? '空托' : '成品';
			$item['zhushi_num'] = $g['zhushi_num'];					
			$item['cpdzcode'] = $g['cpdz_code'];	
			
			$style = strtoupper($g['style_sn']);
			if($style == 'DIA') {
				$item['goods_type'] = 'lz';
			} else if ($style == 'CDIA') {
				$item['goods_type'] = 'caizuan_goods';
			} else if ($g['is_qiban'] == 1) {
				$item['goods_type'] = 'qiban';
				$qb_sn_list[] = $g['goods_id'];
			} else if ($item['is_zp']) {
				$item['goods_type'] = 'zp';
			} else {
				$item['goods_type'] = 'style_goods';
			}
			
			if ($item['goods_type'] == 'lz' || $item['goods_type'] == 'caizuan_goods') {
			    $item['dia_type'] = $g['is_xianhuo'] == 1 ? '1' : '2';	// 如果是裸钻或彩钻，取自身的状态
			} else {
			    $item['dia_type'] = $g['dia_is_xianhuo'] == 1 ? '1' : '2';	// TODO： 再进一步，系统根据证书号查询出货品状态
			}
			
			if ($item_updating) {			
				//$item['modify_time'] = date('Y-m-d H:i:s');
				//if (!empty($g['goods_itemid'])) $item['goods_id'] = $g['goods_itemid']; 布产单回写订单货号，此处不再覆盖				
				$goods['u'][] = $item;
			} else {
				// order_id
			    $item['create_user'] = $order_info['create_user'];
				$item['create_time'] = $order_info['create_time'];
				$item['modify_time'] = $order_info['modify_time'];
				$item['ds_xiangci'] = $g['rec_id']; //存储ishop的订单明细id
				$item['goods_id'] = $g['goods_id'];
				$item['ext_goods_sn'] = $g['style_sn'];
				
				$goods['c'][] = $item;	
			}
			
			//是否总部现货
			if ($g['from_type'] == '6') {
				$zb_xianhuo_list[$g['rec_id']] = $g['goods_id'];
			}
		}
		
		// 剩下的表示要删除；
		$goods['d'] = $existing_items;
	}
	
	//3. 订单付款信息
	$pay = [];
	if ($updating) {
		$pay = $db->getRow("select * from app_order_account where order_id={$order_info['id']} ; ");
	}
	
	// order_id 		
	$pay['order_amount'] = $goods_amount - $data['refund_amount']; //商品明细总金额 - 订单退款金额
	if ($pay['order_amount'] < 0) $pay['order_amount'] = 0;

	$pay['money_paid'] = $data['rcb_amount'];
	if ($pay['money_paid'] < 0) $pay['money_paid'] = 0;

	$pay['money_unpaid'] = $data['order_amount'] - $data['rcb_amount'];	
	if ($pay['money_unpaid'] < 0) $pay['money_unpaid'] = 0;

	$pay['goods_amount'] = $goods_amount;	
	if ($pay['goods_amount'] < 0) $pay['goods_amount'] = 0;

	$pay['shipping_fee'] = 0;
	$pay['favorable_price'] = $zhekou_amount;
	$order_info['order_pay_status'] = $data['pay_status'];
	
	/*
	if (!empty($data['order_pay_action'])) {

		foreach($data['order_pay_action'] as $m) {

			if (empty($order_info['pay_date']) || $order_info['pay_date'] > $m['pay_date']) {
				$order_info['pay_date'] = $m['pay_date'];
			}
		}
	}
	*/
	if ($order_info['is_xianhuo'] == 10) $order_info['is_xianhuo'] = 1; // ensure;
	if ($order_info['is_zp'] == 10) $order_info['is_zp'] = 0; // ensure;

	// 4.订单地址
	$addr = [];
	if ($updating) {
		$addr = $db->getRow("select * from app_order_address where order_id={$order_info['id']} ; ");
	}
	if (!empty($data['extend_order_common'])) {
		$ishop_addr = $data['extend_order_common'];
		$addr['consignee'] = $ishop_addr['reciver_name'];
		$addr['distribution_type'] = 1; //默认都到门店

		$store = $data['extend_store'];
		$addr['shop_type'] = 2; // 默认都是经销商店
		$addr['shop_name'] = $store['store_name'];
		$addr['address'] = $store['store_address'];
		$addr['express_id'] = 10; //与erp保持一致		
	}

	echo PHP_EOL.'-----------------------start process order:' .$order_sn.PHP_EOL;

	// 开启事务
	try {
        $db->beginTransaction();
        if ($updating) {
            $order_id = $order_info['id'];
			$resp = $db->update('base_order_info', $order_info, array('id' => $order_id));
		} else {

        	//不是赠品单
            //if($order_info['is_zp'] != 1){
                //先根据订单审核时再查询所有客户来源一致的没成交的预约间查询是否本月有客户来源一致的订单： 有：绑定预约；
                //没有： ：有：绑定预约；
                //没有：新建预约 //客户级别：A1(crm那边根据门店所属经销商类型来判断)

                $store_id = $data['store_id'];
                $customer_mobile = $data['buyer_phone'];
                $customer_source_id = $order_info['customer_source_id'];
                $start_time = date('Y-m',time())."-01 00:00:00";
                $end_tiem = date('Y-m-d H:i:s',time());

                $order_model = new orderModel();
                $where['store_id'] = $store_id;
                $where['is_zp'] = 0;
                $where['buyer_phone'] = $customer_mobile;
                $where['customer_source_id'] = $customer_source_id;
                $where['audit_time'] = array('BETWEEN',$start_time.",".$end_tiem);
                $orderinfo = $order_model->getOrderInfo($where,array(),'bespoke_id','audit_time desc');
                if(!empty($orderinfo) && $orderinfo['bespoke_id'] !=0 ){
                    $bespoke_id = $orderinfo['bespoke_id'];
                    $order_model->editOrder(['bespoke_id'=>$bespoke_id],array('order_sn'=>$order_sn));
                }else{
                    $bespoke_info = $db_front->getRow("select bespoke_id,re_status from app_bespoke_info where customer_mobile = '{$customer_mobile}' AND department_id ={$store_id} AND  customer_source_id='{$customer_source_id}' AND deal_status =2 ORDER BY create_time desc limit 1");
                    if (!empty($bespoke_info)) {
                        $bespoke_edit['deal_status'] = 1;
                        if($bespoke_info['re_status'] == 2){
                            $bespoke_edit['re_status'] = 1;
                            $bespoke_edit['real_inshop_time'] = date('Y-m-d H:i:s', $data['add_time']);
                        }
                        $bespoke_id = $bespoke_info['bespoke_id'];
                        //更新预约为已成交
                        $order_model->editOrder(['bespoke_id'=>$bespoke_id],array('order_sn'=>$order_sn));
                        $db_front->update('app_bespoke_info', $bespoke_edit, array('bespoke_id' => $bespoke_info['bespoke_id']));
                    }else{

                        $customer = $data['buyer_name'];
                        $mobile = $data['buyer_phone'];
                        $email = $data['buyer_email'];
                        $bespoke_inshop_time =date('Y-m-d', $data['add_time']);
                        //唯一预约号
                        do{
                            $besp_sn=create_besp_sn();
                            $sql="select bespoke_id from app_bespoke_info where `bespoke_sn` = '".$besp_sn."'";
                            $bespoke = $db_front->getRow($sql);
                            if(!$bespoke){
                                break;
                            }
                        }while(true);

                        $newdo['customer']=$customer;
                        $newdo['customer_mobile']=$mobile;
                        $newdo['customer_email']=$email;
                        $newdo['bespoke_sn']=$besp_sn;
                        $newdo['department_id'] = $store_id;
                        $newdo['create_time']=date("Y-m-d H:i:s");
                        $newdo['bespoke_status'] = 2;
                        $newdo['re_status'] = 1;
                        $newdo['bespoke_inshop_time']=$bespoke_inshop_time;
                        $newdo['real_inshop_time']=date("Y-m-d H:i:s",$data['add_time']);
                        $newdo['deal_status'] = 2;
                        $newdo['make_order']=$data['seller_name'];
                        $newdo['accecipt_man']=$data['seller_name'];
                        $newdo['remark']=$data['remark'];
                        $newdo['customer_source_id']=$customer_source_id;
                        $sql="select member_id from base_member_info where `member_phone` = '".$mobile."'";
                        $userInfo = $db_front->getRow($sql);
                        if(!$userInfo){
                            $basemember=array();
                            $basemember['customer_source_id']=$customer_source_id;
                            $basemember['member_name']=$customer;
                            $basemember['department_id']=$store_id;
                            $basemember['member_phone']=$mobile;
                            $basemember['member_email']=$email;
                            $basemember['member_type'] = 1;
                            $basemember['reg_time'] = $data['add_time'];
                            $basemember['make_order'] = $data['seller_name'];
                            $db_front->insert("base_member_info",$basemember);
                            $member_id = $db_front->insertId();

                        }else{
                            $member_id = $userInfo['member_id'];
						}
                        $newdo['mem_id']= $member_id;
                        $db_front->insert("app_bespoke_info",$newdo);
                        $bespoke_id = $db_front->insertId();
                        $order_model->editOrder(['bespoke_id'=>$bespoke_id],array('order_sn'=>$order_sn));
                    }
                }
                $order_info['bespoke_id'] = $bespoke_id;
			//}


			$order_info['referer'] = '智慧门店';
			$resp = $db->insert('base_order_info', $order_info);
			$order_id = $db->insertId();
		}
		
		if ($resp === false) {
			log_order($msgId, $order_sn, '更新或创建订单失败');
			throw new Exception('更新或创建订单失败');
		}

		if (!empty($goods)) {
			if (!empty($goods['c'])) {
			    
			    $goods['c'] = array_map(function($v) use($order_id) {
			        $v['order_id'] = $order_id;
			        return $v;
			    }, $goods['c']);
			    
				$resp = $db->autoExecALL($goods['c'], 'app_order_details');
				if ($resp === false) {
					log_order($msgId, $order_sn, ['err' => '新增订单商品失败', 'goods' => $goods['c']]);
					throw new Exception('新增订单商品失败');
				}
			}
			
			if (!empty($goods['u'])) {
			    $resp = $db->autoExecALL($goods['u'], 'app_order_details', 'UPDATE', array_map(function($g){
					return ['id' => $g['id']];
				}, $goods['u']));
				
				if ($resp === false) {
					log_order($msgId, $order_sn, ['err' => '更新订单商品失败', 'goods' => $goods['u']]);
					throw new Exception('更新订单商品失败');				
				}				
			}
						
			if (!empty($goods['d'])) {

				$ids = [];				
				foreach ($goods['d'] as $g) {
				    $ids[] = $g['id'];
				}
				
				$ids = implode(',', $ids);
				$resp = $db->exec("delete from app_order_details where order_id = {$order_id} and id in ({$ids});");
				if ($resp === false) {
					log_order($msgId, $order_sn, ['err' => '删除订单商品失败', 'goods' => $ids]);
					throw new Exception('删除订单商品失败');			
				}
			}
		}
				
		if (!empty($pay)) {
			$pay['order_id'] = $order_id;
			if (isset($pay['id']) && !empty($pay['id'])) {
				$resp = $db->update('app_order_account', $pay, ['id' => $pay['id'], 'order_id' => $order_id]);
			} else {
				$resp = $db->insert('app_order_account', $pay);
			}
			
			if ($resp === false) {
				log_order($msgId, $order_sn, ['err' => 'upsert订单付款信息失败', 'pay' => $pay]);
				throw new Exception('upsert订单付款信息失败');
			}
		}

		if (!empty($addr)) {
			$addr['order_id'] = $order_id;
			$resp = $db->upsert($addr, 'app_order_address', ['id']);
			
			if ($resp === false) {
			    log_order($msgId, $order_sn, ['err' => 'upsert订单收货信息失败', 'addr' => $addr]);
			    throw new Exception('upsert订单收货信息失败');
			}
		}
        
        $db->commit();
		echo 'order['. $order_sn .'] upsert completed, and id is '.$order_id.' in erp.'.PHP_EOL; 
		       
    } catch(Exception $e) {
        echo $e->getMessage().PHP_EOL;
		$db->rollback();
		
		log_order($msgId, $order_sn, $e->getMessage());
        return false;
	}
	
	// 通知前端系统，订单同步完成
	global $worker;
	$worker->dispatch('ishop', array('event' => 'order_synced', 'order_sn' => $order_sn, 'order_id' => $data['order_id'], 'date' => date('Y-m-d H:i:s'), 'is_xianhuo' => $order_info['is_xianhuo'], 'order_state' => $data['order_state']));
	
	// 绑定或解绑总部现货
	if (!empty($zb_xianhuo_list)) {
		try {
			echo '--start to process zhanting xianhuo'.PHP_EOL;

			$rec_ids = implode(',', array_keys($zb_xianhuo_list));
			$zb_goods = $db->getAll("select id, goods_id, is_return, ds_xiangci from app_order_details where order_id={$order_id} AND ds_xiangci in ({$rec_ids});");
				
			// 切换数据库
			$db->selectDB('warehouse_shipping');
			$db->beginTransaction();

			// 1. 如果订单已取消, 则直接将该订单所绑定的总部现货解绑
			if ($data['order_state'] == 0 && $erp_order_status < 3) {
					
				$resp = $db->exec("update warehouse_goods set order_goods_id = 0, kela_order_sn=NULL where kela_order_sn='{$order_sn}';");
				if ($resp === false){
					log_order($msgId, $order_sn, '解绑总部现货失败！');
					throw new Exception('解绑总部现货失败！');
				}
			} else {
				$list = [];
				$where = [];
				$banding_goods = [];
				// 未发货 已审核
				if ($data['order_state'] >= 5 && $data['order_state'] < 30) {
					
					foreach ($zb_goods as $zg) {
						if (!empty($zg['goods_id']) && is_numeric($zg['goods_id'])) {
							if ($zg['is_return'] == 1) continue;

							$list[] = ['order_goods_id' => $zg['id'], 'kela_order_sn'=> $order_sn];
							$where[] = ['goods_id' => $zg['goods_id'], 'kela_order_sn' => $order_sn];
							$banding_goods[] = $zg['goods_id'];
						}
					}
				}
					
				$jiebang_sql = '';
				if (!empty($list)) {
					// 添加绑定关系
					$resp = $db->autoExecALL($list, 'warehouse_goods', 'UPDATE', $where);
					if ($resp === false) {
						log_order($msgId, $order_sn, ['err' => '绑定总部现货失败！', 'where'=> $where]);
						throw new Exception('绑定总部现货失败！');
					}

					$banding_goods = implode(',', $banding_goods);
					$jiebang_sql = "update warehouse_goods set order_goods_id = 0,kela_order_sn = NULL where kela_order_sn = '{$order_sn}' AND company_id = 58 and goods_id not in ({$banding_goods});";
				} else {
					$jiebang_sql = "update warehouse_goods set order_goods_id = 0,kela_order_sn = NULL where kela_order_sn = '{$order_sn}' AND company_id = 58";
				}
					
				// 解绑
				$resp = $db->exec($jiebang_sql);
				if ($resp === false) {
					log_order($msgId, $order_sn, ['err' => '解绑总部现货失败！', 'sql'=> $jiebang_sql]);
					throw new Exception('解绑总部现货失败！');
				}
				
				$db->commit();
			}
		} catch(Exception $ex) {
			echo $ex->getMessage().PHP_EOL;
			$db->rollback();
			
			log_order($msgId, $order_sn, $ex->getMessage());
			return false;
		}
	} 
	
	/**
	 * 起版号，需要修改使用状态
	 */
	$num = count($qb_sn_list);
	if ($num > 0) {
		$db->selectDB('purchase');
		
		if ($num == 1) {
			$db->exec("update purchase_qiban_goods set order_sn = '{$order_sn}', customer = '{$data['buyer_name']}', opt = '{$data['seller_name']}' where addtime = {$qb_sn_list[0]};");
		} else {
			$qb_sn_list = implode(',', $qb_sn_list);
			$db->exec("update purchase_qiban_goods set order_sn = '{$order_sn}', customer = '{$data['buyer_name']}', opt = '{$data['seller_name']}' where addtime in ({$qb_sn_list});");
		}
	}
			
	echo PHP_EOL.'=============  ORDER SYNC DONE!!!  =============='.PHP_EOL;
	return true;           
}

function resolve_kezi($kezi) {
	/*
	if (strpos($kezi, '&') !== false) {
		return str_replace('&', '[&符号]', $kezi);
	} else if (strpos($kezi, '•') !== false) {
		return str_replace('•', '[间隔号]', $kezi);
	} else if (strpos($kezi, '♡') !== false) {
		return str_replace('♡', '[空心]', $kezi);
	} else if (strpos($kezi, '♥') !== false) {
		return str_replace('♥', '[实心]', $kezi);
	}
	*/
	return $kezi;
}

function log_order($msgId, $order_sn, $err) {
    file_put_contents(__DIR__.'/'.date('Y-m-d').'_erp.sync_order.err', json_encode(['msgId' => $msgId, 'err' =>$err, 'order_sn'=> $order_sn], JSON_UNESCAPED_UNICODE).PHP_EOL, FILE_APPEND);
}

/**
 * 生成预约号
 */
 function create_besp_sn(){
	return date('ym').str_pad(mt_rand(1,99999),5,'0',STR_PAD_LEFT);
}



?>
