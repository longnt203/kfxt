<?php
Tools::import('Action_ActionBase');
class ActionGame_MasterTools_ItemCardDownLoad_GongFu extends Action_ActionBase{

	public function _init(){
	}
	public function getPostData($post=null){
		$this->_classId = intval($_GET['classId']);
		$this->_batchId = intval($_GET['batchId']);
		return array(
			'classId'=>$this->_classId,
			'batchId'=>$this->_batchId,
		);
	}
	public function main($UrlAppend=null,$get=null,$post=null){
		$postData = $this->getPostData($post);
		$getData = $this->_gameObject->getGetData($get);
		unset($getData['serverId']);	//这个接口不用serverId这个参数
		$data = $this->getResult($UrlAppend,$getData,$postData);
		
		if($data['status'] !=1){
			$this->_assign['connectError'] = 'Error Info:'.$data['info'];
			$this->stop($this->_assign['connectError'],-1);
		}else{
			header("Content-Type: text/plain");
			header("Content-Disposition: attachment; filename=card_".CURRENT_TIME.".txt");
			foreach($data["data"] as $value){
				echo $value["secretKey"];
				echo "\r\n";
			}
		}
		exit();
		$this->stop('',-1);
	}
}

//"$data" = Array [3]	
//	data = Array [1]	
//		0 = Array [10]	
//			id = (int) 11	
//			classId = (int) 110	
//			serverId = (string:2) S4	
//			playerId = (int) 1	
//			batchId = (int) 570	
//			secretKey = (string:36) 0c7c6e3c-4e97-4092-9694-9c9441c05d29	
//			addTime = (int) 1324954264	
//			playerName = null	
//			playerAccount = null	
//			exchangeTime = (int) 0	
//	status = (int) 1	
//	info = null	

