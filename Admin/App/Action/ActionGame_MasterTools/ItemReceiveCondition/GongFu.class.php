<?php
Tools::import('Action_ActionBase');
class ActionGame_MasterTools_ItemReceiveCondition_GongFu extends Action_ActionBase{
	
	private $_effectiveTime = 604800;	//7天，缓存有效时间，超时自动更新
	
	public function _init(){
		
	}
	
	public function main($UrlAppend=null,$get=null,$post=null){
		if(!$_REQUEST['server_id']){
			return array();
		}
		$effectiveTime = $this->_effectiveTime;
		if($_REQUEST['timeout']){
			$effectiveTime = -1;
		}
		$fileCacheName = $this->getFileCacheName();
		$items = $this->_f($fileCacheName,'',CACHE_DIR,$effectiveTime);
		if(false !== $items){
			return $items;
		}
		$items = $this->_getData($UrlAppend,$get,$post);
		$updateSuccess = 0;
		if($items){
			$updateSuccess = 1;
			$this->_f($fileCacheName,$items);
		}
		if($_REQUEST['timeout']){
			if($this->_isAjax()){	//ajax的更新提示
				$this->ajaxReturn(array('status'=>$updateSuccess,'info'=>null,'data'=>null));
			}elseif($updateSuccess){
				$this->jump('操作成功',1);
			}else{
				$this->jump('操作失败',-1,1,false);
			}
		}
		return $items;
	}
	
	
	private function _getData($UrlAppend=null,$get=null,$post=null){
		$getData = $get;
		$getData['serverId'] = $this->_gameObject->getServerId();
		$data = $this->getResult($UrlAppend,$getData);
		$itemsData = array();
		if($data['status'] != '1'){
			return $itemsData;
		}
		$itemsData = $data['data'];
		return $itemsData;
	}

	
	
	
}