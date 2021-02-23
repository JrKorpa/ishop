var order_id,order_goods_id,goods_pay_price;
$(function(){
	var key = getCookie('key');
	if(!key){
		window.location.href = WapSiteUrl+'/login.html';		
	}
    $.getJSON(ApiUrl + '/index.php?act=member_refund&op=refund_form',{key:key,order_id:getQueryString('order_id'),order_goods_id:getQueryString('order_goods_id')}, function(result) {
        if(result.code!=200){
		   $(".alert-danger p").html(result.datas.error);
		   $("form").html("");
		   return false;																																														       
		}
		checkLogin(result.login);
        result.datas.WapSiteUrl = WapSiteUrl;
        $('#order-info-container').html(template.render('order-info-tmpl',result.datas));
        
        order_id = result.datas.order.order_id;
        order_goods_id = result.datas.goods.order_goods_id;
        
        // 退款原因
	    var _option = '';
	    for (var k in result.datas.reason_list) {
	        _option += '<option value="' + result.datas.reason_list[k].reason_id + '">' + result.datas.reason_list[k].reason_info + '</option>'
	    }
	    $('#refundReason').append(_option);
	    
	    // 可退金额
	    goods_refund_price = result.datas.goods.goods_refund_price;
	    $('input[name="refund_amount"]').val(goods_refund_price);
	    $('#returnAble').html('￥'+goods_refund_price);
	    // 图片上传
        $('input[name="refund_pic"]').ajaxUploadImage({
            url : ApiUrl + "/index.php?act=member_refund&op=upload_pic",
            data:{key:key},
            start :  function(element){
                element.parent().after('<div class="upload-loading"><i></i></div>');
                element.parent().siblings('.pic-thumb').remove();
            },
            success : function(element, result){
                checkLogin(result.login);
                if (result.datas.error) {
                    element.parent().siblings('.upload-loading').remove();
                    $.sDialog({
                        skin:"red",
                        content:'图片尺寸过大！',
                        okBtn:false,
                        cancelBtn:false
                    });
                    return false;
                }
                element.parent().after('<div class="pic-thumb"><img src="'+result.datas.pic+'"/></div>');
                element.parent().siblings('.upload-loading').remove();
                element.parents('a').next().val(result.datas.file_name);
            }
        });

        $('input[name="return_type"]').click(function(){
            if($(this).val() == 2){
                $("#weiyue").show();
            }else{
                $("#weiyue").hide();
            }
        });

        /*$('input[name="refund_amount"]').blur(function(){
            var refund_amount = $(this).val();
            var weiyue_amount = new Number(goods_refund_price-refund_amount).toFixed(2)
            $('input[name="breach_amount"]').val(weiyue_amount);
        });
        
        $('input[name="breach_amount"]').blur(function(){
            var breach_amount = $(this).val();
            var weiyue_amount = new Number(goods_refund_price-breach_amount).toFixed(2)
            $('input[name="refund_amount"]').val(weiyue_amount);
        });*/

        $('.btn-l').click(function(){
            var _form_param = $('form').serializeArray();
            var param = {};
            param.key = key;
            param.order_id = order_id;
            param.order_goods_id = order_goods_id;
            param.refund_type = 1;
            for (var i=0; i<_form_param.length; i++) {
                param[_form_param[i].name] = _form_param[i].value;
            }
            if (!param.return_type) {
                $.sDialog({
                    skin:"red",
                    content:'请选择退款方式',
                    okBtn:false,
                    cancelBtn:false
                });
                return false;
            }
            if (isNaN(parseFloat(param.refund_amount)) || parseFloat(param.refund_amount) > parseFloat(goods_refund_price)) {
                $.sDialog({// || parseFloat(param.refund_amount) == 0
                    skin:"red",
                    content:'退款金额不能为空，或不能超过可退金额',
                    okBtn:false,
                    cancelBtn:false
                });
                return false;
            }
            var breach_amount = parseFloat(param.breach_amount)?parseFloat(param.breach_amount):0;
            if ((parseFloat(param.refund_amount)+parseFloat(breach_amount)) > parseFloat(goods_refund_price)) {
                $.sDialog({// || parseFloat(param.refund_amount) == 0
                    skin:"red",
                    content:'退款金额+违约金不能超过可退金额',
                    okBtn:false,
                    cancelBtn:false
                });
                return false;
            }
            if (param.buyer_message.length == 0) {
                $.sDialog({
                    skin:"red",
                    content:'请填写退款说明',
                    okBtn:false,
                    cancelBtn:false
                });
                return false;
            }
            // 退款申请提交
            $.ajax({
                type:'post',
                url:ApiUrl+'/index.php?act=member_refund&op=refund_post',
                data:param,
                dataType:'json',
                async:false,
                success:function(result){
                    console.log(result.datas);
                    checkLogin(result.login);
                    if (result.datas.error) {
                        $.sDialog({
                            skin:"red",
                            content:result.datas.error,
                            okBtn:false,
                            cancelBtn:false
                        });
                        return false;
                    }else{
                        $.sDialog({
                            skin:"red",
                            content:"提交成功",
                            okBtn:false,
                            cancelBtn:false
                        });

                        setTimeout(function(){
                            window.location.href = WapSiteUrl + '/tmpl/member/order_detail.html?order_id='+order_id;
                        },2000); 
                        //var url = WapSiteUrl + '/tmpl/member/order_detail.html?order_id='+order_id;
                        //window.setTimeout(url,2000);
                    }
                }
            });
        });
    });
});