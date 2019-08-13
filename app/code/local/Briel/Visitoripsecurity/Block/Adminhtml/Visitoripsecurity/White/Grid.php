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
class Briel_Visitoripsecurity_Block_Adminhtml_Visitoripsecurity_White_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		
		$this->setId('whiteGrid');
		// This is the primary key of the database
		//$this->setDefaultSort('url_c');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(false);
		
		//echo 'asd';used
	}
protected function _prepareCollection(){
		
		
		$model = Mage::getModel('visitoripsecurity/log_remoteaddr_notes');
		$collection = $model->getCollection();
		
		$rs = Mage::getSingleton('core/resource');
		
		$visitor = $rs->getTableName('log_visitor');
		$visitor_online = $rs->getTableName('log_visitor_online');
		$visitor_info = $rs->getTableName('log_visitor_info');
		
		$log_url = $rs->getTableName('log_url');
		$log_url_info = $rs->getTableName('log_url_info');
		$ip_notes = $rs->getTableName('log_remoteaddr_notes');
		
		$select = $collection->getSelect();
		
		$select->where('main_table.white = 1');
				
		$this->setCollection($collection);
		
		//$collection->printLogQuery(true);
		
		//die();
		
		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{

		$this->addColumn('url_c', array(
				'header'    => Mage::helper('visitoripsecurity')->__('Count'),
				'width'     => '50px',
				'align'		=> 'center',
				'renderer'  => 'visitoripsecurity/adminhtml_visitoripsecurity_renderer_count',
				'sortable'	=> false,
				'filter'=> false
		));
		$this->addColumn('remote_addr', array(
				'header'    => Mage::helper('visitoripsecurity')->__('IP address'),
				'index'     => 'remote_addr',
				'width'     => '80px',
				'align'		=> 'center',
				'renderer'  => 'visitoripsecurity/adminhtml_visitoripsecurity_renderer_google'
		));
		
		$this->addColumn('action',
			array(
					'header'    =>  Mage::helper('customer')->__('View'),
					'width'     => '50px',
					'type'      => 'action',
					'getter'    => 'getRemoteAddr',
					'align'		=> 'center',
					'actions'   => array(
						array(
								'caption'   => Mage::helper('customer')->__('View'),
								'url'       => array('base'=> '*/*/logurl'),
								'field'     => 'remote_addr'
						)
					),
					'filter'    => false,
					'sortable'  => false,
					'index'     => 'view',
					'is_system' => true,
			));
		$this->addColumn('note', array(
		
				'header'    => Mage::helper('visitoripsecurity')->__('Notes'),
				'align'     => 'left',
				'width'     => '80px',
				'index'     => 'note',
				'type'      => 'text',
				'align'		=> 'center',
				'renderer'  => 'visitoripsecurity/adminhtml_visitoripsecurity_renderer_notes'
		));
		
		$this->addColumn('whitelist',
				array(
						'header'    =>  Mage::helper('customer')->__('Whitelist'),
						'width'     => '50px',
						'type'      => 'action',
						'getter'    => 'getId',
						'align'		=> 'center',
						'filter'    => false,
						'sortable'  => false,
						'index'     => 'whitelist',
						'is_system' => true,
						'renderer'  => 'visitoripsecurity/adminhtml_visitoripsecurity_renderer_whitethisip'
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
					$field = new Zend_Db_Expr('INET_NTOA(main_table.remote_addr)');
					$this->getCollection()->addFieldToFilter($field , $cond);
				}
				
				return $this;
			}elseif ($column->getId() == 'last_visit_at') {
				
				$cond = $column->getFilter()->getCondition();
				if(!empty($cond)){
					$field = 'main_table.last_visit_at';
					$this->getCollection()->addFieldToFilter($field , $cond);
				}
				
				return $this;
			}elseif ($column->getId() == 'first_visit_at') {
				
				$cond = $column->getFilter()->getCondition();
				
				
				if(!empty($cond)){
					$field = 'main_table.first_visit_at';
					$this->getCollection()->addFieldToFilter($field , $cond);
				}
				
				return $this;
			}elseif ($column->getId() == 'note') {
				
				$cond = $column->getFilter()->getCondition();
				if(!empty($cond)){
					$field = 'main_table.note';
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
		return $this->getUrl('*/*/white', array('_current'=>true));
	}


}