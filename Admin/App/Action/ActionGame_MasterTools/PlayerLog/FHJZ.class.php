<?php
Tools::import('Action_ActionBase');
class ActionGame_MasterTools_PlayerLog_FHJZ extends Action_ActionBase{
	
	private $_playerLogTypes = array();
	private $userType = array();
	
	public function _init(){
		$this->_assign['URL_LogTypeUpdate']= $this->_urlLogTypeUpdate();
		$this->_playerLogTypes = $this->partner('PlayerLogType');	//从日志类型Action接口中拿数据
		$this->userType = Tools::gameConfig('userType',$this->_gameObject->_gameId);
		$this->_assign['playerLogTypes'] = json_encode($this->_playerLogTypes);
		$this->_assign['userType'] = $this->userType;
	}
	
	public function getPostData($post=NULL){
		$postData = array(
			'pageSize'=>PAGE_SIZE,
			'page'=>max(1,intval($_GET['page'])),
			'user' => trim($_GET['user']),
			'userType' => trim($_GET['userType']),
			'keywords'=>trim($_GET['keywords']),
			'objectId'=>intval($_GET['objectId']),
			'eventId'=>intval($_GET['eventId']),
			'beginTime'=>strtotime(trim($_GET['StartTime'])),
			'endTime'=>strtotime(trim($_GET['EndTime'])),
		);
		if($post){
			$postData = array_merge($post,$postData);
		}
		$postData = array_filter($postData,'strlen');
		return $postData;
	}
	
	public function main($UrlAppend=null,$get=null,$post=null){
		if(!$_REQUEST['server_id'] || !$_REQUEST['submit']){
			return $this->_assign;
		}
		
		$playerLogTypes = $this->_playerLogTypes;
		$postData = $this->getPostData($post);
		$postData = $this->_gameObject->getPostData($postData);
		$data = $this->getResult($UrlAppend,$get,$postData);
		
		if($data['status'] == '1'){
			$this->_assign['playerName'] = $data['data']['playerName'];
			$this->_assign['playerAccount'] = $data['data']['playerAccount'];
			foreach($data['data']['list'] as &$sub){
				$sub['addTime'] = date("Y-m-d H:i:s",$sub['addTime']/1000);
				$sub['logType'] = $playerLogTypes[$sub['objectId']]['rootTypeName'].'-->'.$playerLogTypes[$sub['objectId']]['subTypeList'][$sub['eventId']]['subTypeName'];
			}
			$this->_assign['dataList'] = $data['data']['list'];
			$this->_loadCore('Help_Page');//载入分页工具
			$helpPage=new Help_Page(array('total'=>$data['data']['total'],'perpage'=>PAGE_SIZE));
			$this->_assign['pageBox'] = $helpPage->show();
		}else{
			$this->_assign['connectError'] = 'Error Message:'.$data['info'];
		}
		return $this->_assign;
	}
	
	private function _urlLogTypeUpdate(){
		$query = array(
			'zp'=>PACKAGE,
			'__game_id'=>$this->_gameObject->_gameId,
			'server_id'=>$_REQUEST['server_id'],
			'timeout'=>1,
		);
		return Tools::url(CONTROL,'PlayerLogType',$query);
	}
}