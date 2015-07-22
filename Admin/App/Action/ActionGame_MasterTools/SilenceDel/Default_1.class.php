<?php
Tools::import('Action_ActionBase');
class ActionGame_MasterTools_SilenceDel_Default_1 extends Action_ActionBase{

	private $_utilMsg;
	protected $_serverId;
	protected $_players;
	protected $_playerType;
	protected $_endTime = '';
	protected $_cause;
	protected $_playersData=array();
	protected $_silenceDel = true;
	public function _init(){
		$this->_utilMsg = $this->_getGlobalData('Util_Msg','object');
	}

	public function main($UrlAppend=NULL,$get=NULL,$post=NULL){
		if(!$_REQUEST['server_id']){
			return array();
		}
		$this->_serverId = intval($_REQUEST['server_id']);
		$this->_players = trim($_GET['User']);
		$this->_playerType = 1;
		$this->_cause = '';
		$this->_playersData[]=array(
							'playerId'=>trim($_GET['User']),
		);

		$getData = $this->_gameObject->getGetData($get);
		$getData["User"]	=	$_GET['User'];
		$data = $this->getResult($UrlAppend,$getData);
		if($data["states"]=="1"){
			$jumpUrl = $this->_urlNotice();
			$this->jump('操作成功',1,$jumpUrl);
		}else{
			$errorInfo = '操作失败:';
			$this->jump($errorInfo.$data['info'],-1);
		}
	}

	private function _urlNotice(){
		$query = array(
			'zp'=>PACKAGE,
			'__game_id'=>$this->_gameObject->_gameId,
			'server_id'=>$_REQUEST['server_id'],
		);
		return Tools::url(CONTROL,'Silence',$query);
	}
}