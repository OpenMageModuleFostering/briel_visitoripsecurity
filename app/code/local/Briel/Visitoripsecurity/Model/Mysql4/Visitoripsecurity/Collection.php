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
class Briel_Visitoripsecurity_Model_Mysql4_Visitoripsecurity_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	
	public function getSelectCountSql()
	{
		$this -> _renderFilters();
	
		$rs = Mage::getSingleton('core/resource');
		
		//$log_visitor_info = $rs->getTableName('log_visitor_info');
		$ip_notes = $rs->getTableName('log_remoteaddr_notes');
		
		//echo 'visitoripsecurity collection';
		$countSelect = clone $this -> getSelect();
		$countSelect -> reset(Zend_Db_Select::ORDER);
		$countSelect -> reset(Zend_Db_Select::LIMIT_COUNT);
		$countSelect -> reset(Zend_Db_Select::LIMIT_OFFSET);
		$countSelect -> reset(Zend_Db_Select::COLUMNS);
	
		$countSelect -> from('', 'COUNT(DISTINCT vi.remote_addr)');
		
		$countSelect -> resetJoinLeft();
		
		//$countSelect -> joinLeft(array('lrn'=>$ip_notes), 'lrn.remote_addr=vi.remote_addr', array('blocked','white','note'));
        $countSelect -> reset('group');
       	$countSelect -> reset('having');

		return $countSelect;
	}
	
	
}