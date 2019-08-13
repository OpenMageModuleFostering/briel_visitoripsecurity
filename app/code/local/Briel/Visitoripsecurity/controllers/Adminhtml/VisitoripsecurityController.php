<?php
/**
 * Briel Software SRL
 * http://www.briel.ro
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to radu@briel.ro so we can send you a copy immediately.
 *
 * @category    Briel
 * @package     Visitoripsecurity
 * @author      Radu Parvan <radu@briel.ro>
 * @copyright   Copyright (c) 2012 (http://www.briel.ro)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Briel_Visitoripsecurity_Adminhtml_VisitoripsecurityController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction()
	{
		$this->loadLayout()
		->_setActiveMenu('visitoripsecurity/items')
		->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		return $this;
	}
	 
	public function indexAction() {
		
		$this->_initAction();
		//$this->_addContent($this->getLayout()->createBlock('visitoripsecurity/adminhtml_visitoripsecurity_grid'));
		$this->renderLayout();
		//exit();
		
	}
	
	public function logurlAction() {
	
		$this->_initAction();
		//$this->_addContent($this->getLayout()->createBlock('visitoripsecurity/adminhtml_visitoripsecurity_logurl_grid'));
		$this->renderLayout();
		//exit();
	
	}
	public function blockedAction() {
	
		$this->_initAction();
		//$this->_addContent($this->getLayout()->createBlock('visitoripsecurity/adminhtml_visitoripsecurity_blocked_grid'));
		$this->renderLayout();
		//exit();
	
	}
	
	public function whiteAction() {
	
		$this->_initAction();
		//$this->_addContent($this->getLayout()->createBlock('visitoripsecurity/adminhtml_visitoripsecurity_blocked_grid'));
		$this->renderLayout();
		//exit();
	
	}
	public function watchAction() {
	
		$this->_initAction();
		//$this->_addContent($this->getLayout()->createBlock('visitoripsecurity/adminhtml_visitoripsecurity_watch_grid'));
		$this->renderLayout();
		//exit();
	
	}
	public function oneipAction() {
	
		$this->_initAction();
		$this->_addContent($this->getLayout()->createBlock('visitoripsecurity/adminhtml_visitoripsecurity_oneip_block'));
		$this->renderLayout();
		//exit();
	
	}

	public function updateNoteAction()
	{
		$remote_addr = (int) $this->getRequest()->getParam('id');
		$note = $this->getRequest()->getParam('note');
		
		
		if ($remote_addr) {
			$rs = Mage::getSingleton('core/resource');
			$table = $rs->getTableName('log_remoteaddr_notes');
			
			//$model2 = Mage::getModel('visitoripsecurity/log_remoteaddr_notes');
			 
			$model = Mage::getModel('visitoripsecurity/log_remoteaddr_notes');
			$collection = $model->getCollection();
			$select = $collection->getSelect();
			$select->where("remote_addr = '".$remote_addr."'");
			
			
			$arrData = $collection->getData();
			
			//var_dump($arrData);
			
			if(!empty($arrData)){
				//update
				$model->load($arrData[0]['id']);
				$model->setData('note', $note);
				$model->save();
				
			}else{
				//insert
				
				$model = Mage::getModel('visitoripsecurity/log_remoteaddr_notes');
				$data = array('remote_addr'=>$remote_addr, 'note'=>$note);
				$model->setData($data);
				$model->save();
				
			}
			
		}
	}
	
	public function blockOneIpAction()
	{
	
		$ip = $this->getRequest()->getParam('ip');
		$ip = trim($ip);
		if(stripos($ip, '.') !== false){
			$long_ip = ip2long($ip);
		}else{
			$long_ip = $ip;
			$ip = long2ip($ip);
		}
		
		$cacheFile = Mage::getBaseDir('cache') . '/blocked_ips.dat';
	
		$arrBlocked = Mage::helper('visitoripsecurity')->getBlocked();
		if(!Mage::helper('visitoripsecurity')->checkIfBlocked($ip)){
			if(!Mage::helper('visitoripsecurity')->checkIfWhite($ip)){
				array_push($arrBlocked, $ip);
			}else{
				echo $html = $this->__('IP ').$ip.$this->__(' is in whitelist');
				return;
			}
		}else{
			echo $html = $this->__('IP ').$ip.$this->__(' is allready blocked');
			return;
		}
	
		file_put_contents($cacheFile,serialize($arrBlocked));
	
		echo 'blocked';
	}
	public function unblockOneIpAction()
	{
	
		$ip = $this->getRequest()->getParam('ip');
		$ip = trim($ip);
		if(stripos($ip, '.') !== false){
			$long_ip = ip2long($ip);
		}else{
			$long_ip = $ip;
			$ip = long2ip($ip);
		}
	
		$cacheFile = Mage::getBaseDir('cache') . '/blocked_ips.dat';
		$tmp_arr = array();
		$arrBlocked = Mage::helper('visitoripsecurity')->getBlocked();
		if(Mage::helper('visitoripsecurity')->checkIfBlocked($ip)){
			foreach($arrBlocked as $k => $v){
				if($v != trim($ip)){
					array_push($tmp_arr, $v);
				}
			}
			$arrBlocked = $tmp_arr;
		}else{
			echo $html = 'IP '.$ip.' is not blocked';
			return;
		}
	
		file_put_contents($cacheFile,serialize($arrBlocked));
	
		echo $html = 'IP '.$ip.' was unblocked';
	}
	public function blockThisIpAction()
	{
		
		$ip = $this->getRequest()->getParam('ip');
		$ip = trim($ip);
		$long_ip = $ip;
		$ip = long2ip($ip);
		$cacheFile = Mage::getBaseDir('cache') . '/blocked_ips.dat';
		
		$arrBlocked = Mage::helper('visitoripsecurity')->getBlocked();
		if(!Mage::helper('visitoripsecurity')->checkIfBlocked($ip)){
			if(!Mage::helper('visitoripsecurity')->checkIfWhite($ip)){
				array_push($arrBlocked, $ip);
			}else{
				echo '';
				return;
			}
		}
		
		file_put_contents($cacheFile,serialize($arrBlocked));
		
		$model = Mage::getModel('visitoripsecurity/log_remoteaddr_notes');
		$collection = $model->getCollection();
		$select = $collection->getSelect();
		$select->where("remote_addr = '".$long_ip."'");
		
		$arrData = $collection->getData();
			
		//var_dump($arrData);
			
		if(!empty($arrData)){
			//update
			$model->load($arrData[0]['id']);
			$model->setData('blocked', '1');
			$model->save();
		
		}else{
			//insert
		
			$model = Mage::getModel('visitoripsecurity/log_remoteaddr_notes');
			$data = array('remote_addr'=>$long_ip, 'blocked'=>'1');
			$model->setData($data);
			$model->save();
		
		}
		
		echo $html = '<button onclick="unBlockThisIp(this, '. $long_ip .'); return false">' . Mage::helper('visitoripsecurity')->__('Unblock') . '</button>';
	}
	public function whiteThisIpAction()
	{
	
		$ip = $this->getRequest()->getParam('ip');
		$ip = trim($ip);
		$long_ip = $ip;
		$ip = long2ip($ip);
		$cacheFile = Mage::getBaseDir('cache') . '/white_ips.dat';
	
		$arrWhite = Mage::helper('visitoripsecurity')->getWhite();
		if(!Mage::helper('visitoripsecurity')->checkIfWhite($ip)){
			if(!Mage::helper('visitoripsecurity')->checkIfBlocked($ip)){
				array_push($arrWhite, $ip);
			}else{
				echo '';
				return;
			}
		}
	
		file_put_contents($cacheFile,serialize($arrWhite));
		
		$model = Mage::getModel('visitoripsecurity/log_remoteaddr_notes');
		$collection = $model->getCollection();
		$select = $collection->getSelect();
		$select->where("remote_addr = '".$long_ip."'");
		
		$arrData = $collection->getData();
			
		//var_dump($arrData);
			
		if(!empty($arrData)){
			//update
			$model->load($arrData[0]['id']);
			$model->setData('white', '1');
			$model->save();
		
		}else{
			//insert
		
			$model = Mage::getModel('visitoripsecurity/log_remoteaddr_notes');
			$data = array('remote_addr'=>$long_ip, 'white'=>'1');
			$model->setData($data);
			$model->save();
		
		}
		
		
		echo $html = '<button onclick="unWhiteThisIp(this, '. $long_ip .'); return false">' . Mage::helper('visitoripsecurity')->__('Remove white') . '</button>';
	}
	public function watchThisIpAction()
	{
	
		$ip = $this->getRequest()->getParam('ip');
		$ip = trim($ip);
		$long_ip = $ip;
		$ip = long2ip($ip);
	
		$model = Mage::getModel('visitoripsecurity/log_remoteaddr_notes');
		$collection = $model->getCollection();
		$select = $collection->getSelect();
		$select->where("remote_addr = '".$long_ip."'");
	
		$arrData = $collection->getData();
			
		//var_dump($arrData);
			
		if(!empty($arrData)){
			//update
			$model->load($arrData[0]['id']);
			$model->setData('watch', '1');
			$model->save();
	
		}else{
			//insert
	
			$model = Mage::getModel('visitoripsecurity/log_remoteaddr_notes');
			$data = array('remote_addr'=>$long_ip, 'watch'=>'1');
			$model->setData($data);
			$model->save();
	
		}
	
	
		echo $html = '<button onclick="unWhatchThisIp(this, '. $long_ip .'); return false">' . Mage::helper('visitoripsecurity')->__('Remove whatch') . '</button>';
	}
	public function unBlockThisIpAction()
	{
	
		$ip = $this->getRequest()->getParam('ip');
		$ip = trim($ip);
		$long_ip = $ip;
		$ip = long2ip($ip);
		$arrIp = explode('.', $ip);
		$cacheFile = Mage::getBaseDir('cache') . '/blocked_ips.dat';
	
		$arrBlocked = Mage::helper('visitoripsecurity')->getBlocked();
		$arrData = array();
		if(Mage::helper('visitoripsecurity')->checkIfBlocked($ip)){
			foreach ($arrBlocked as $v){
				
				$tmp_ip = explode('.', $v);
				$found = 0;
				foreach($tmp_ip as $key => $val){
					if($val == '*'){
						$found++;
					}elseif($arrIp[$key] == $val){
						$found++;
					}
				}
				if($found != 4){
					array_push($arrData, $v);
				}
				
			}
		}else{
			$arrData = $arrBlocked;
		}

		
		file_put_contents($cacheFile,serialize($arrData));
		
		$model = Mage::getModel('visitoripsecurity/log_remoteaddr_notes');
		$collection = $model->getCollection();
		$select = $collection->getSelect();
		$select->where("remote_addr = '".$long_ip."'");
		
		$arrData = $collection->getData();
			
		//var_dump($arrData);
			
		if(!empty($arrData)){
			//update
			$model->load($arrData[0]['id']);
			$model->setData('blocked', '0');
			$model->save();
		
		}else{
			//insert
		
			$model = Mage::getModel('visitoripsecurity/log_remoteaddr_notes');
			$data = array('remote_addr'=>$long_ip, 'blocked'=>'0');
			$model->setData($data);
			$model->save();
		
		}
		
		echo $html = '<button onclick="blockThisIp(this, '. $long_ip .'); return false">' . Mage::helper('visitoripsecurity')->__('Block') . '</button>';
	}
	public function unWhiteThisIpAction()
	{
	
		$ip = $this->getRequest()->getParam('ip');
		$ip = trim($ip);
		$long_ip = $ip;
		$ip = long2ip($ip);
		$arrIp = explode('.', $ip);
		$cacheFile = Mage::getBaseDir('cache') . '/white_ips.dat';
	
		$arrWhite = Mage::helper('visitoripsecurity')->getWhite();
		$arrData = array();
		if(Mage::helper('visitoripsecurity')->checkIfWhite($ip)){
			foreach ($arrWhite as $v){
	
				$tmp_ip = explode('.', $v);
				$found = 0;
				foreach($tmp_ip as $key => $val){
					if($val == '*'){
						$found++;
					}elseif($arrIp[$key] == $val){
						$found++;
					}
				}
				if($found != 4){
					array_push($arrData, $v);
				}
	
			}
		}else{
			$arrData = $arrWhite;
		}
	
	
		file_put_contents($cacheFile,serialize($arrData));
		
		$model = Mage::getModel('visitoripsecurity/log_remoteaddr_notes');
		$collection = $model->getCollection();
		$select = $collection->getSelect();
		$select->where("remote_addr = '".$long_ip."'");
		
		$arrData = $collection->getData();
			
		//var_dump($arrData);
			
		if(!empty($arrData)){
			//update
			$model->load($arrData[0]['id']);
			$model->setData('white', '0');
			$model->save();
		
		}else{
			//insert
		
			$model = Mage::getModel('visitoripsecurity/log_remoteaddr_notes');
			$data = array('remote_addr'=>$long_ip, 'white'=>'0');
			$model->setData($data);
			$model->save();
		
		}
		
		echo $html = '<button onclick="whiteThisIp(this, '. $long_ip .'); return false">' . Mage::helper('visitoripsecurity')->__('Whitelist') . '</button>';
	}
	public function unWatchThisIpAction()
	{
	
		$ip = $this->getRequest()->getParam('ip');
		$ip = trim($ip);
		$long_ip = $ip;
		$ip = long2ip($ip);
	
		$model = Mage::getModel('visitoripsecurity/log_remoteaddr_notes');
		$collection = $model->getCollection();
		$select = $collection->getSelect();
		$select->where("remote_addr = '".$long_ip."'");
	
		$arrData = $collection->getData();
			
		//var_dump($arrData);
			
		if(!empty($arrData)){
			//update
			$model->load($arrData[0]['id']);
			$model->setData('watch', '0');
			$model->save();
	
		}else{
			//insert
	
			$model = Mage::getModel('visitoripsecurity/log_remoteaddr_notes');
			$data = array('remote_addr'=>$long_ip, 'watch'=>'0');
			$model->setData($data);
			$model->save();
	
		}
	
		echo $html = '<button onclick="whatchThisIp(this, '. $long_ip .'); return false">' . Mage::helper('visitoripsecurity')->__('Whatchlist') . '</button>';
	}
	
}