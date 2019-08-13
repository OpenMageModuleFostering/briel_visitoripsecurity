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
class Briel_Visitoripsecurity_Block_Adminhtml_Visitoripsecurity_Logurl_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		
		$this->setId('logurlGrid');
		// This is the primary key of the database
		$this->setDefaultSort('visit_time');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(false);
		
		//echo 'asd';used
	}
	protected function _prepareCollection(){
		
		//var_dump($this->getRequest());
		
		$model = Mage::getModel('visitoripsecurity/log_url');
		$collection = $model->getCollection();
		
		$rs = Mage::getSingleton('core/resource');
		
		$visitor_online = $rs->getTableName('log_visitor_online');
		$visitor_info = $rs->getTableName('log_visitor_info');
		$log_url = $rs->getTableName('log_url');
		$log_url_info = $rs->getTableName('log_url_info');
		$ip_notes = $rs->getTableName('log_remoteaddr_notes');
		
		$select = $collection->getSelect();
		
		$select->joinLeft(array('lui'=>$log_url_info), 'main_table.url_id=lui.url_id',array('url'));
		$select->joinLeft(array('vi'=>$visitor_info), 'main_table.visitor_id=vi.visitor_id',array('remote_addr'));
		
		$select->where("vi.remote_addr = '".$this->getRequest()->getParam('remote_addr')."'");
		
		$this->setCollection($collection);
		
		//$collection->printLogQuery(true);
		
		
		
		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		
		$this->addColumn('id', array(
				'header'    => Mage::helper('visitoripsecurity')->__('ID'),
				'width'     => '50px',
				'index'     => 'url_id',
				'align'		=> 'center',
		));
		$this->addColumn('url', array(
				'header'    => Mage::helper('visitoripsecurity')->__('Last url'),
				'align'     => 'left',
				'width'     => '120px',
				'type'      => 'text',
				'default'   => '--',
				'index'     => 'url',
				'renderer'  => 'visitoripsecurity/adminhtml_visitoripsecurity_renderer_pretty'
		));
		$this->addColumn('visit_time', array(
				'header'    => Mage::helper('visitoripsecurity')->__('Visit Time'),
				'align'     => 'center',
				'width'     => '120px',
				'type'      => 'datetime',
				'default'   => '--',
				'index'     => 'visit_time',
				'sortable'  => true
		));
		$this->addColumn('remote_addr', array(
				'header'    => Mage::helper('visitoripsecurity')->__('IP address'),
				'index'     => 'remote_addr',
				'width'     => '80px',
				'align'		=> 'center',
				'renderer'  => 'visitoripsecurity/adminhtml_visitoripsecurity_renderer_google'
		));
		
		
		
		//$this->print_a($this);
		//var_dump($this);
		
		return parent::_prepareColumns();
	}
	protected function _addColumnFilterToCollection($column)
	{
		$rs = Mage::getSingleton('core/resource');
		
		$visitor_online = $rs->getTableName('log_visitor_online');
		$visitor_info = $rs->getTableName('log_visitor_info');
		$log_url = $rs->getTableName('log_url');
		$log_url_info = $rs->getTableName('log_url_info');
		$ip_notes = $rs->getTableName('log_remoteaddr_notes');
		
		if ($this->getCollection()) {
			if ($column->getId() == 'remote_addr') {
				
				$cond = $column->getFilter()->getCondition();
				if(!empty($cond)){
					$field = new Zend_Db_Expr('INET_NTOA(vi.remote_addr)');
					$this->getCollection()->addFieldToFilter($field , $cond);
				}
				
				return $this;
			}elseif ($column->getId() == 'visit_time') {
				
				$cond = $column->getFilter()->getCondition();
				if(!empty($cond)){
					$field = 'main_table.visit_time';
					$this->getCollection()->addFieldToFilter($field , $cond);
				}
				
				return $this;
			}elseif ($column->getId() == 'id') {
				
				$cond = $column->getFilter()->getCondition();
				if(!empty($cond)){
					$field = 'main_table.url_id';
					$this->getCollection()->addFieldToFilter($field , $cond);
				}
				
				return $this;
			}else{
				return parent::_addColumnFilterToCollection($column);
			}
		}
	}
	public function getRowUrl($row)
	{
		//return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}

	public function getGridUrl()
	{
		return $this->getUrl('*/*/logurl', array('_current'=>true));
	}


}