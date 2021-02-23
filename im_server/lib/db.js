var mysql = require('mysql');
var db_config = require('../config');
var c = console.log;
var db = '';
var tablepre = db_config.config['tablepre'];

db = mysql.createConnection(db_config.config);


function handleDisconnect(db) {
  db.on('error', function(err) {
    if (!err.fatal) {
      return;
    }
    
    if (err.code !== 'PROTOCOL_CONNECTION_LOST') {
      throw err;
    }
    console.log('Re-connecting lost mysql: ' + err.code);
    db = mysql.createConnection(db.config);
    handleDisconnect(db);
    db.connect();
  });
}
handleDisconnect(db);


db.connect(function(err) {
  //if (typeof err != 'undefined' && err != null) throw console.log('mysql err: ' + err.code);
  console.log('   mysql connected');
});

function get_query(sql) {
	var db_query = '';
	
	if ( typeof sql === "object" ) {
		var n = 0;
		for (var k in sql){
			if ( typeof sql[k] === "string" ) {
				n++;
				db_query += k+"='"+db.escape(sql[k])+"' AND ";
			}
		}
		db_query += ' 2 > 1';
	} else {
		db_query = sql;
	}
	
	return db_query;
}

exports.get_msg_list = function (sql, dbcb) {
	var db_query = 'SELECT * FROM ';
	var table = tablepre+'chat_msg ';
	db_query += table+' WHERE ';
	
	db_query += get_query(sql);
	db.query(db_query, function(err, rows) {
        dbcb(rows);
	});
};

exports.del_msg = function (sql) {
	var db_query = 'DELETE FROM ';
	var table = tablepre+'chat_msg ';
	db_query += table+' WHERE ';
	
	db_query += get_query(sql);
	db.query(db_query, function(err, rows) {
		
	});
};

exports.update_msg = function (sql, values) {
	var db_query = 'UPDATE ';
	var table = tablepre+'chat_msg SET ';
	db_query += table;
	for (var k in values){
		db_query += k+"='"+db.escape(values[k])+"' ";
	}
	db_query += ' WHERE ';
	
	db_query += get_query(sql);
	db.query(db_query, function(err, rows) {
	  
	});
};

/**
 * 获取指定店铺的客服人员
 * @param store_id 店铺ID
 * @param final_call　回调函数
 */
exports.get_all_customer_server = function(store_id, final_call) {
	//１、 跟进store_id获取对应的客服数据
	var store_info_query = "SELECT " + tablepre + "streo_presales FROM store WHERE store_id = '" + store_id + "'";
    db.query(store_info_query, function (err, rows) {
        var cs_ids = [];
        if(typeof rows == 'undefined') {
            return false;s
        }
        rows.forEach(function (key, item) {
            cs_ids.push(item.store)
        });
        //２、跟进ＩＤ
        var db_query = "SELECT m.member_id, m.member_name, m.member_avatar, st.store_id, st.store_name, st.store_avatar, st.grade_id, s.seller_id " +
            "FROM " + tablepre + "member AS m " +
            "LEFT JOIN " + tablepre + "seller s ON m.member_id = s.member_id " +
            + " WHERE m.member_id IN (" + cs_ids.join(",") +")";
        db.query(db_query, final_call);
    });
};

/**
 * 获取指定店铺的客服人员
 * @param store_id　店铺ID
 * @param u_id 用户ID
 * @param cs_ids　客服IDs
 * @param final_call 回调
 */
exports.get_latest_customer_server = function(store_id, u_id, cs_ids, final_call) {
    var db_query = "SELECT m.member_id, m.member_name, m.member_avatar, st.store_id, st.store_name, st.store_avatar, st.grade_id, s.seller_id " +
        "FROM " + tablepre + "chat_msg cm " +
        "LEFT JOIN " + tablepre + "member AS m ON m.member_id = cm.f_id " +
        "LEFT JOIN " + tablepre + "seller s ON m.member_id = s.member_id " +
        "LEFT JOIN " + tablepre + "seller_group sg ON s.seller_group_id = sg.group_id " +
        "LEFT JOIN " + tablepre + "store st ON s.store_id = st.store_id " +
        "WHERE sg.is_customer_server = 1 AND s.store_id = '" + store_id + "' AND cm.t_id = '" + u_id + "'" +
        "ORDER BY cm.add_time DESC LIMIT 1;";
    db.query(db_query, final_call);
};
