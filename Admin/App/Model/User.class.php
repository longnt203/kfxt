<?php
/**
 * 用户表
 * @author php-朱磊
 */
class Model_User extends Model {
	protected $_tableName = 'user';	//表名
	
	private $_cacheFile;
	
	private $_cacheAllUserFile;	//所有用户文件缓存
	
	private $_checkLoginIndexFile;	//用户名缓存keyvalue文件,用户登录快速索引
	
	private $_userIdIndex;
	
	public function __construct() {
		$this->_cacheFile = CACHE_DIR . "/{$this->_tableName}.cache.php";
		$this->_cacheAllUserFile=CACHE_DIR . "/{$this->_tableName}_all.cache.php";
		$this->_checkLoginIndexFile=CACHE_DIR . "/{$this->_tableName}_index.cache.php";
		$this->_userIdIndex=CACHE_DIR . "/{$this->_tableName}_index_id.cache.php";
	}
	
	/**
	 * 获取部门所有用户
	 * @param int $departmentId
	 */
	public function findByDepartment($departmentId){
		$dataList=$this->select("select * from {$this->tName()} where department_id ={$departmentId}");
		return $dataList;
	}
	/**
	 * 获取所有用户
	 * @author doter
	 */
	public function getAllUser(){
		$dataList=$this->select("select * from {$this->tName()} where status = 1");
		return $dataList;
	}
	/**
	 * 根据用户名查找用户资料
	 * @param string $userName 用户名
	 * @return array
	 */
	public function findByUserName($userName){
		$sql="select * from {$this->tName()} where user_name='{$userName}'";
		return $this->select($sql,1);
	}
	
	/**
	 * 通过ids查找所有的用户
	 * @param array $ids
	 */
	public function findByUsersToCache($ids){
		$users=$this->_getGlobalData('user');
		$tmpArr=array();
		foreach ($ids as $value){
			$tmpArr[$value]=$users[$value];
		}
		return $tmpArr;
	}
	
	/**
	 * 删除用户账号
	 * @param string $userName
	 */
	public function delUser($userName){
		$userDetail=$this->findByUserName($userName);
		if (!$userDetail)return array('msg'=>"用户: {$userName} 不存在",'status'=>-2,'href'=>1);
		$userDir = USERS_DIR . "/{$_GET['user_name']}";
		$userClassPath=$userDir.'/Info.serialize.php';
		$userMailPath=$userDir.'/Mail.serialize.php';
		if (file_exists($userClassPath))unlink($userClassPath);
		if (file_exists($userMailPath))unlink($userMailPath);
		if (is_dir($userDir))rmdir($userDir);
		$this->execute("delete from {$this->tName()} where user_name='{$userName}' limit 1");
		return array('msg'=>"删除用户: {$userName} 成功",'status'=>1,'href'=>1);
		
	}
	
	/**
	 * 通过$id查找组名为$id和没有被分配组的的用户,如果$id不存在就查找org为空没有加入组的
	 * @param int $id
	 */
	public function findOrgByUser($id=NULL){
		if ($id===null){//查找没有加入org组别的用户
			return $this->select("select Id,org_id,department_id,user_name,nick_name from {$this->tName()} where org_id=0");
		}else {//查找org等于$id的组别
			return $this->select("select Id,org_id,department_id,user_name,nick_name from {$this->tName()} where org_id={$id} or org_id=0");
		}
	}
	
	/**
	 * 查找所有在组内的用户
	 * @param boolean $isCache 是否搜索缓存
	 */
	public function findSetOrgByUser($isCache=TRUE){
		if ($isCache){
			$allUser=$this->_getGlobalData('user');
			$orgUser=array();
			foreach ($allUser as $value){
				if ($value['org_id'])array_push($orgUser,$value);
			}
			return $orgUser;
		}else {
			return $this->select("select Id,org_id,department_id,user_name,nick_name from {$this->tName()} where org_id!=0 order by org_id");
		}
	}
	
	/**
	 * 通过org_id查找用户
	 * @param int $orgId
	 * @param boolean $isCache 是否通过缓存查找
	 */
	public function findByOrgId($orgId,$isCache=true){
		if (!$orgId)return false;
		if ($isCache){
			$allUsers=$this->_getGlobalData('user');
			$orgUser=array();
			if (!$allUsers)return false;
			foreach ($allUsers as $value){
				if ($value['org_id']==$orgId)array_push($orgUser,$value);
			}
			return $orgUser;
		}else {
			return $this->select("select Id,org_id,department_id,user_name,nick_name from {$this->tName()} where org_id={$orgId}");
		}
	}
	
	public function createCache() {
		$dataList =$this->select("select Id,org_id,department_id,service_id,roles,user_name,nick_name,order_vip_level,status,project_id,position_id from {$this->tName()} order by department_id,org_id");
		if (!$dataList)return false;
		$departmentList=$this->_getGlobalData('department');
		$departmentList=Model::getTtwoArrConvertOneArr($departmentList,'Id','name');
		$orgList=$this->_getGlobalData('org');
		$orgList=Model::getTtwoArrConvertOneArr($orgList,'Id','name');
		
		$userIdIndex=array();
		$checkLoginIndex=array();
		$tmpArr=array();//正式的用户数组(不包括被停用的)
		$allUserCache=array();//所有的用户数组
		$num=count($dataList);
		//建立缓存时,让条记录的ID,成为这个数组的key值,方便后期优化
		for ($i=0;$i<$num;$i++){
			$userIdIndex[$dataList[$i]['Id']]=$dataList[$i]['nick_name'];
			$checkLoginIndex[$dataList[$i]['user_name']]=$dataList[$i]['nick_name'];
			$dataList[$i]['full_name']=$dataList[$i]['nick_name'];
			if ($dataList[$i]['org_id'])$dataList[$i]['full_name'].="({$orgList[$dataList[$i]['org_id']]})";
			$dataList[$i]['full_name'].="[{$departmentList[$dataList[$i]['department_id']]}]";
			
			if ($dataList[$i]['status']==0){
				$dataList[$i]['nick_name']=$dataList[$i]['nick_name'].' [<font color="#FF0000">停用</font>]';
				$dataList[$i]['full_name']=$dataList[$i]['full_name'].' [<font color="#FF0000">停用</font>]';
			}
			
			if ($dataList[$i]['status']==1)$tmpArr[$dataList[$i]['Id']]=$dataList[$i];	//如果status=1 的话才会加入正式的cache文件
			$allUserCache[$dataList[$i]['Id']]=$dataList[$i];
		}
		
		$this->_addCache($userIdIndex,$this->_userIdIndex);	//ID为key的cache文件
		$this->_addCache($checkLoginIndex,$this->_checkLoginIndexFile);
		$this->_addCache($allUserCache,$this->_cacheAllUserFile);	//cache缓存文件
		return $this->_addCache ( $tmpArr, $this->_cacheFile );
	}
	
}