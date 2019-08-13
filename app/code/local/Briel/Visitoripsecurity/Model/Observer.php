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
class Briel_Visitoripsecurity_Model_Observer {
	
	protected $_sendEmails = null;
	protected $_sendEmailsTo = null;
	protected $_firstVisitsLimit = null;
	protected $_secondVisitsLimit = null;
	
	public function checkBlocked() {
		
		
		$this->_sendEmails = Mage::getStoreConfig('brielvisitoripsecurity/visitoripsecurityadmin/send_emails');
		$this->_sendEmailsTo = Mage::getStoreConfig('brielvisitoripsecurity/visitoripsecurityadmin/email_to');
		$this->_firstVisitsLimit = Mage::getStoreConfig('brielvisitoripsecurity/visitoripsecurityadmin/first_limit');
		$this->_secondVisitsLimit = Mage::getStoreConfig('brielvisitoripsecurity/visitoripsecurityadmin/second_limit');
		
		if(empty($this->_sendEmailsTo)){
			$this->_sendEmailsTo = Mage::getStoreConfig('trans_email/ident_general/email');
		}
		
		$helper = Mage::helper ( 'visitoripsecurity' );
		
		$ip = $_SERVER ['REMOTE_ADDR'];
		if ($helper->checkIfBlocked ( $ip )) {
			header ( 'HTTP/1.0 403 Access Denied/Forbidden' );
			exit ();
		} elseif ($helper->checkIfWhite ( $ip )) {
			return;
		}
		
		
		if($this->_sendEmails){
			
			$this->sendFirstLimit($ip);
			
			$this->sendSecondLimit($ip);
			
		}
		

	}
	
	public function sendFirstLimit($ip){
		
		$collection = Mage::getModel ( 'log/visitor' )->getCollection ();
		
		$rs = Mage::getSingleton ( 'core/resource' );
		
		$ip_notes = $rs->getTableName ( 'log_remoteaddr_notes' );
		$table_visitor_info = $rs->getTableName ( 'log_visitor_info' );
		$table_log_url = $rs->getTableName ( 'log_url' );
		
		$select = $collection->getSelect ();
		$select->reset ( 'from' );
		$select->reset ( 'columns' );
		$select->from ( array ('main_table' => $table_visitor_info ), array ('visitor_id', 'remote_addr' ) );
		
		$select->joinLeft ( array ('lu' => $table_log_url ), 'lu.visitor_id = main_table.visitor_id', array ('url_count' => new Zend_Db_Expr ( 'COUNT(lu.url_id)' ) ) );
		$select->where ( "main_table.remote_addr = '" . ip2long ( $ip ) . "'" );
		$select->where ( "lu.visit_time > DATE(NOW() - INTERVAL 1 DAY)" );
		// $collection->printLogQuery(true);
		
		$arrData = $collection->getData ();
		//var_dump($arrData);
		$arr = array ();
		
		foreach ( $arrData as $k => $v ) {
			$html = '';
			if ($v ['url_count'] > $this->_firstVisitsLimit) {
		
				$model = Mage::getModel ( 'visitoripsecurity/log_remoteaddr_notes' );
				$collection = $model->getCollection ();
				$select = $collection->getSelect ();
				$select->where ( "remote_addr = '" . ip2long ( $ip ) . "'" );
			
				$tmpData = $collection->getData ();
				$html .= '<br /> IP: ' . long2ip ( $v ['remote_addr'] ) . ' - visits: ' . $v ['url_count'];
		
				if (! empty ( $tmpData )) {
						
					if ($tmpData [0] ['first_email_sent_at'] == 0) {

						$time_now = time ();
						$model->load ( $tmpData [0] ['id'] );
						$model->setData ( 'first_email_sent_at', time () );
						$model->save ();
						$this->sendNoticeEmail ( $html );
		
					}
				}else{
        			//insert into notes
        			//echo $html;
        			$data = array('remote_addr'=>ip2long($ip), 'note'=>'','first_email_sent_at'=>time());
        			$model->setData($data);
        			$model->save();
        			$this->sendNoticeEmail($html);
        		}
			}
		}
		
	}
	public function sendSecondLimit($ip){
		
		
		$collection = Mage::getModel ( 'log/visitor' )->getCollection ();
		
		$rs = Mage::getSingleton ( 'core/resource' );
		
		$ip_notes = $rs->getTableName ( 'log_remoteaddr_notes' );
		$table_visitor_info = $rs->getTableName ( 'log_visitor_info' );
		$table_log_url = $rs->getTableName ( 'log_url' );
		
		$select = $collection->getSelect ();
		$select->reset ( 'from' );
		$select->reset ( 'columns' );
		$select->from ( array ('main_table' => $table_visitor_info ), array ('visitor_id', 'remote_addr' ) );
		
		$select->joinLeft ( array ('lu' => $table_log_url ), 'lu.visitor_id = main_table.visitor_id', array ('url_count' => new Zend_Db_Expr ( 'COUNT(lu.url_id)' ) ) );
		$select->where ( "main_table.remote_addr = '" . ip2long ( $ip ) . "'" );
		$select->where ( "lu.visit_time > DATE(NOW() - INTERVAL 7 DAY)" );
		// $collection->printLogQuery(true);
		
		$arrData = $collection->getData ();
		//var_dump($arrData);
		$arr = array ();
		
		foreach ( $arrData as $k => $v ) {
			$html = '';
			if ($v ['url_count'] > $this->_secondVisitsLimit) {
		
				$model = Mage::getModel ( 'visitoripsecurity/log_remoteaddr_notes' );
				$collection = $model->getCollection ();
				$select = $collection->getSelect ();
				$select->where ( "remote_addr = '" . ip2long ( $ip ) . "'" );
		
				$tmpData = $collection->getData ();
				$html .= '<br /> IP: ' . long2ip ( $v ['remote_addr'] ) . ' - visits: ' . $v ['url_count'];
		
				if (! empty ( $tmpData )) {
						
					if ($tmpData [0] ['second_email_sent_at'] == 0) {
						$time_now = time ();
		
						$model->load ( $tmpData [0] ['id'] );
						$model->setData ( 'second_email_sent_at', time () );
						$model->save ();
						$this->sendNoticeEmail ( $html );
					}
				}else{
        			//insert into notes
        			//echo $html;
        			$data = array('remote_addr'=>ip2long($ip), 'note'=>'','second_email_sent_at'=>time());
        			$model->setData($data);
        			$model->save();
        			$this->sendNoticeEmail($html);
        		}
			}
		}
		
	}
	
	public function sendNoticeEmail($body) {
		
		$mail = Mage::getModel ( 'core/email' );
		
		$mail->setToName ( 'admin' );
		
		$mail->setBody ( $body );
		$mail->setSubject ( 'IP to watch' );
		$mail->setFromEmail ( $this->_sendEmailsTo );
		$mail->setFromName ( "Customer flood notice" );
		$mail->setType ( 'html' );
		
		try {
			$mail->setToEmail ( $this->_sendEmailsTo );
			$mail->send ();
			
		} catch ( Exception $e ) {
		
		}
	
	}

}