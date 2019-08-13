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
class Briel_Visitoripsecurity_Block_Adminhtml_Visitoripsecurity_Renderer_Count extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		/* Get The Email Address Using query From Model Using id of the customer as
		 $id=$row->getId();
		*/
		 
		$rs = Mage::getSingleton('core/resource');
		 
		$visitor_info = $rs->getTableName('log_visitor_info');
		$log_url = $rs->getTableName('log_url');
		$log_url_info = $rs->getTableName('log_url_info');
		 
		 
		$collection = Mage::getModel('log/visitor')->getCollection();
		$this->setCollection($collection);
		$select = $collection->getSelect();
		$select->reset('from');
		$select->from(array('main_table' => $visitor_info));
		$select->where("remote_addr = '".$row->_data['remote_addr']."'");
		 
		//$collection->printLogQuery(true);
		 
		$arrData = $collection->getData();
		if(empty($arrData)){
			return '';
		}
		$in = "(";
		foreach ($arrData as $k =>$v){
			$in .= "'".$v['visitor_id']."',";
		}
		$in = trim($in, ",");
		$in .= ")";
		 
		$collection = Mage::getModel('log/visitor')->getCollection();
		$this->setCollection($collection);
		$select = $collection->getSelect();
		$select->reset();
		$select->from(array('main_table' => $log_url), array(new Zend_Db_Expr('COUNT(main_table.url_id) AS url_c')));
		$select->where(new Zend_Db_Expr('main_table.visitor_id IN'.$in));
		//$collection->printLogQuery(true);

		$arrUrls = $collection->getData();
		
		if(!empty($arrUrls)){
			//var_dump($arrUrls);
			$url_c = $arrUrls[0]['url_c'];
		}else{
			$url_c = '0';
		}
		
		 

		return '<div style="word-wrap: break-word;">'.$url_c.'</div>';
	}
}

?>