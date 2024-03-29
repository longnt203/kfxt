<?php /* Smarty version 2.6.26, created on 2013-04-07 16:59:05
         compiled from ActionGame_MasterTools/GameLogin/HuanJL.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', 'ActionGame_MasterTools/GameLogin/HuanJL.html', 80, false),)), $this); ?>
<script language="javascript">
function chageOperator(val){
	if(val > 0){
		$('#showForm').show();
	}else{
		$('#showForm').hide();
	}
	showServerList(val);
}
var serverlist = <?php echo $this->_tpl_vars['serverlist']; ?>
;
function showServerList(operatorId){
	if(operatorId>0){
		$('#serverlist_select').html('');
		$('#serverlist_select').append('<option value="0"> -检索- </option>');
		$.each(serverlist,function(id,val){
			if(val.operator_id ==operatorId){
				$('#serverlist_select').append('<option value="'+val.Id+'">'+val.server_name+' => '+val.ordinal+'</option>');
			}
		});
	}
}
function serverSelected(server_id){
	if(server_id>0){
		$('input[name=ordinal]').val(serverlist[server_id].ordinal);
	}
}
function ordinalInputed(ordinal){
	if(ordinal>=0){
		var operator_id = $('select[name=operator_id]').val();
		$.each(serverlist,function(id,son){
			if(son.operator_id == operator_id && son.ordinal == ordinal){
				$('select[name=server_id]').val(son.Id);
				return false;
			}
		});
	}
}
function checkEmpty(){
	var noEmpty = new Array("cause","ordinal");
	var explain = new Array("操作原因","服务器号");
	for (x in noEmpty)
	{
		var obj = $('[name='+noEmpty[x]+']');
		var content = $.trim(obj.val());
		if(content=='' ){
			alert(explain[x]+' 不能为空！');
			obj.focus();
			return false;
		}
	}
	var ordinal = $('[name=ordinal]').val();
	if(parseInt(ordinal)>-1){
		var playerAccount = $('[name=playerAccount]').val();
		var type = $('[name=type]').val();
		if(type == 1){
			if(isNaN(playerAccount)){
				alert("用户id是数字");
				return false;
			}
		}
	}else{
		alert('服务器号必须大于-1');
		$('[name=ordinal]').focus();
		return false;
	}
	return true;
}
$(function(){
	$('.playerv_alue').hide().first().show();
});
function selectPlayer(obj){
	$('.playerv_alue').hide();$('input[name='+obj.val()+']').parent().show();
}
</script>
<fieldset>
  <legend>游戏登录</legend>
  <form action="" method="post" target="_blank">
    <select id="operator" name="operator_id" onChange="chageOperator($(this).val())">
    	<option value="0">请选择运营商</option>
        <?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['operatorList']), $this);?>

    </select>
  <div id="showForm" style="display:none">
    <table width="100%" border="0" cellpadding="3">
      <tr>
        <th scope="col">操作原因</th>
        <td><textarea name="cause" style="width:400px; height:60px;"></textarea></td>
       </tr>
      <tr>
        <th scope="col">服务器号</th>
        <td>
        	<input type="text" class="text" name="ordinal" onblur="ordinalInputed($(this).val())" />
        	<select name="server_id" id="serverlist_select" onchange="serverSelected($(this).val())">
        		<option value="0"> -检索- </option>
        	</select>
            说明：两控件互相绑定，但以下拉框为准
        </td>
      </tr>
      
      <tr>
        <th scope="col">玩家</th>
        <td>
             	账号类型：
             <select name="type">
            	<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['loginIdentifier'],'selected' => $this->_tpl_vars['_POST']['type']), $this);?>

            </select>
             	玩家： <input type="text" class="text" style="width:400px;" name="playerAccount" />      
        </td>
      </tr>
      
      <tr>
        <th scope="col">&nbsp;</th>
        <td><input class="btn-blue" type="submit" onclick="return checkEmpty();" value="登录" /></td>
      </tr>
    </table>
    
  </div>
</fieldset>