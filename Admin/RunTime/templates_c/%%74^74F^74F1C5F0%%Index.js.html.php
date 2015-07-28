<?php /* Smarty version 2.6.26, created on 2013-04-08 18:10:35
         compiled from QualityCheck/Index.js.html */ ?>
<link href="<?php echo $this->_tpl_vars['__JS__']; ?>
/Libs/My97DatePicker/skin/WdatePicker.css" rel="stylesheet" type="text/css">
<script language="javascript" src="<?php echo $this->_tpl_vars['__JS__']; ?>
/Libs/My97DatePicker/WdatePicker.js"></script>
<script language="javascript">
var users=eval(<?php echo $this->_tpl_vars['users']; ?>
);

function viewUser(orgId){
	var userList=$("#userList");
	userList.empty(); 
	$.each(users,function(i,n){
		if(n.org_id==orgId){
			userList.append('<input type="checkbox" value="'+n.Id+'" name=users[] />'+n.full_name+'&nbsp;');
		}
	})
}
$(function(){ 
	$(".order_title").hover(function(){$(this).addClass("td_move")},function(){$(this).removeClass("td_move")});
	$(".order_title").one('click',function(){
		var curTd=$(this);
		$.getJSON(
			$(this).attr("url"),
			function(data){
				if(data.status==1){
					var tmpStr='';
					$.each(data.data,function(i,n){
						if(n.qa==0){
							tmpStr+='<div style="background:#FFE1E1; border:1px dashed #CCC; padding:10px; padding-top:3px; margin:10px;">'+n.create_time+'&nbsp;<font style="font-weight:bold">玩家提问：</font><br />'+n.content+'</div>';
						}else{
							tmpStr+='<div style="background:#D9FFDC; border:1px dashed #CCC; padding:10px; padding-top:3px; margin:10px; margin-left:50px;">';
							if(n.is_timeout==1)tmpStr+='<font color="#FF0000"><b>已超时</b></font>';
							if(n.is_quality!=0){
								tmpStr+='<a qa_id="'+n.Id+'" href="javascript:void(0)" onclick="showQuality($(this))">察看质检</a>&nbsp;';
							}else{
								tmpStr+='<font color="#999999">[未质检]</font>&nbsp;';
							}
							
							tmpStr+=n.word_reply_name+'&nbsp;'+n.create_time+'&nbsp;<font style="font-weight:bold">回复：</font><br />'+n.content+'</div>';

						}
					});
					curTd.parent().after("<tr style='background:#FFF' id='"+curTd.attr("dialogId")+"'><td colspan='8'>"+tmpStr+"</td></tr>");
				}
			}
		);
	});
	
	$(".order_title").click(function(){
		var id=$(this).attr("dialogId");
		$("#"+id).toggle();
	})
})
</script>