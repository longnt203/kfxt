<?php /* Smarty version 2.6.26, created on 2013-04-09 14:24:45
         compiled from ActionGame_OperatorTools/AddPlayerPay/SanFen.html */ ?>
<?php if ($this->_tpl_vars['tplServerSelect']): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['tplServerSelect'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<fieldset>
	<legend>添加GM</legend>
	<script language="javascript">
    $(function(){
        $("#isAllUser").change(function(){
        	  if( $(this).attr('value') == '1'){
        		  $("#showuser").hide();
        	  }else{
        		  $("#showuser").show();
        	  }
        });
    	$.formValidator.initConfig({formid:"form",onerror:function(){return false;}});
    	$("#cause").formValidator({onshow:"请输入申请理由",oncorrect:"申请理由正确"}).inputValidator({min:1,empty:{leftempty:false,rightempty:false,emptyerror:"请输入申请理由"},onerror:"申请理由不能为空"});
    })

	function onSubmit(){
    		var $title = $('#title').attr('value');
    		var $content = $('#content').attr('value');
    		var $isAllUser = $("#isAllUser").attr('value');
    		if( $("#gameServer").attr('value') == undefined){
    			alert('请选择服务器！'); return false;
    		}
    		return true;
    		
    		if($title == ''){
    			alert('标题不能空！'); return false;
    		}
    		if($content == ''){
    			alert('内容不能空！'); return false;
    		}

       		if($isAllUser == 0){
       			if($("#player").attr('value') == ''){
       				alert('请填写用户！'); return false;
       			}
       		}
    		$('.returnTip').remove();	//去掉旧提示
    		$(":checkbox[name='server_ids[]']:checked").each(function(i,n){
    				var curLi=$("#server_"+n.value);
    				$("#form").ajaxSubmit({
    					dataType:'json',
    					async : false,    // 设置同步
    					data:{server_id:n.value},
    					success:function(data){
    						var fontColor=(data.status==1)?'#00cc00':'#ff0000';
    						curLi.append("<font class='returnTip' color='"+fontColor+"'> "+data.info+"</font>");
    					}
    				});
    		});
    	}
    </script>
    <form id="form" action="" method="post">
    <table width="100%" border="0" cellpadding="3">
          <tr>
    <th scope="row">申请理由</th>
    <td><textarea name="cause" cols="50" id="cause" rows="8"></textarea><div id="causeTip"></div></td>
  </tr>
      <tr>
        <th scope="row">玩家ID：</th>
        <td>
            <input type="text" name="playerId" id="playerId" class="text"/>
        </td>
      </tr>
      <tr>
        <th scope="row">游戏币：</th>
        <td>
            <input type="text" name="payPrice" id="payPrice" class="text"/>
        </td>
      </tr>
      <tr>
        <th colspan="2" scope="row"><input type="submit" onclick="onSubmit();" class="btn-blue" name="sbm" value="立即发送" /></th>
      </tr>
    </table>
    </form>
    </fieldset>