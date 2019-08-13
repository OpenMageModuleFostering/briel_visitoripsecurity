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
class Briel_Visitoripsecurity_Block_Adminhtml_Visitoripsecurity_Watch_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
public function __construct()
	{
		parent::__construct();
		
		$this->setId('watchGrid');
		// This is the primary key of the database
		$this->setDefaultSort('url_c');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(false);
		
		$this->_headerText = Mage::helper('visitoripsecurity')->__('Ip logs');
		
	}
	protected function _prepareCollection(){
		
		
		$model = Mage::getModel('log/visitor');
		$collection = $model->getCollection();
		
		$rs = Mage::getSingleton('core/resource');
		
		$visitor = $rs->getTableName('log_visitor');
		$visitor_online = $rs->getTableName('log_visitor_online');
		$visitor_info = $rs->getTableName('log_visitor_info');
		
		$log_url = $rs->getTableName('log_url');
		$log_url_info = $rs->getTableName('log_url_info');
		$ip_notes = $rs->getTableName('log_remoteaddr_notes');
		
		$select = $collection->getSelect();
		$select->reset('columns');
		$select->reset('from');
		
		$select->from(array('main_table'=>$visitor), array('main_table.visitor_id', 'MAX(main_table.last_visit_at) AS last_visit_time'));
		
		$select->joinLeft(array('vi'=>$visitor_info), 'vi.visitor_id=main_table.visitor_id', 'vi.remote_addr');
		$select->joinLeft(array('lu'=>$log_url), 'lu.visitor_id=main_table.visitor_id', array('url_c'=>'COUNT(*)'));
		$select->joinLeft(array('lui'=>$log_url_info), 'lui.url_id=last_url_id', array('last_url'=>'lui.url'));
		$select->joinLeft(array('lrn'=>$ip_notes), 'lrn.remote_addr=vi.remote_addr', array('blocked','white','note','watch'));
		
		$select->group('vi.remote_addr');
		$select->having('lrn.watch=1');
		
		
		//$collection->addFieldToSelect(new Zend_Db_Expr('t.*'));
		//$collection->addAtributeToFilter('url_c');
		//$collection->addFieldToSelect(new Zend_Db_Expr($visitor_online.'.last_visit_at AS last_vis_at'));
		//$collection->getSelect()->joinLeft(new Zend_Db_Expr('(SELECT '.$log_url.'.url_id, '.$log_url.'.visitor_id, '.$log_url.'.visit_time AS last_visit_time FROM '.$log_url.' ORDER BY '.$log_url.'.visit_time DESC LIMIT 1)'), 't.visitor_id = main_table.visitor_id');
		
		//$collection->getSelect()->joinLeft(new Zend_Db_Expr('(SELECT COUNT('.$log_url.'.url_id) AS url_c,'.$log_url.'.visitor_id  FROM '.$log_url.')'), 't_2.visitor_id = main_table.visitor_id');
		
		//$collection->getSelect()->joinLeft($visitor_info, $visitor_info.'.visitor_id = main_table.visitor_id');
		//$collection->getSelect()->joinLeft($visitor_online, $visitor_online.'.visitor_id = main_table.visitor_id');
		//$collection->getSelect()->joinLeft($log_url_info, $log_url_info.'.url_id = t.url_id');
		//$collection->getSelect()->joinLeft(array('ip' => $ip_notes), 'ip.remote_addr = '.$visitor_info.'.remote_addr', array('note'));
		//$collection->getSelect()->group($visitor_info.'.remote_addr');
		//$collection->addFieldToSelect(new Zend_Db_Expr($log_url_info.'.url'), 'last_url');
		
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
				'index'     => 'url_c',
				'align'		=> 'center',
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
		
		$this->addColumn('block',
				array(
						'header'    =>  Mage::helper('customer')->__('Block'),
						'width'     => '50px',
						'type'      => 'action',
						'getter'    => 'getId',
						'align'		=> 'center',
						'filter'    => false,
						'sortable'  => false,
						'index'     => 'block',
						'is_system' => true,
						'renderer'  => 'visitoripsecurity/adminhtml_visitoripsecurity_renderer_blockthisip'
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
		$this->addColumn('watchlist',
				array(
						'header'    =>  Mage::helper('customer')->__('Watchlist'),
						'width'     => '50px',
						'type'      => 'action',
						'getter'    => 'getId',
						'align'		=> 'center',
						'filter'    => false,
						'sortable'  => false,
						'index'     => 'watchlist',
						'is_system' => true,
						'renderer'  => 'visitoripsecurity/adminhtml_visitoripsecurity_renderer_watchthisip'
				));
		
		$this->addColumn('first_visit_at', array(
				'header'    => Mage::helper('visitoripsecurity')->__('First visit Time'),
				'align'     => 'center',
				'width'     => '120px',
				'type'      => 'datetime',
				'default'   => '--',
				'index'     => 'first_visit_at',
				'sortable'  => true,
				'renderer'  => 'visitoripsecurity/adminhtml_visitoripsecurity_renderer_firstvisit'
		));
		
		$this->addColumn('last_visit_at', array(
				'header'    => Mage::helper('visitoripsecurity')->__('Last visit Time'),
				'align'     => 'center',
				'width'     => '120px',
				'type'      => 'datetime',
				'default'   => '--',
				'index'     => 'last_visit_time',
				'sortable'  => true
		));
		
		$this->addColumn('last_url', array(
				'header'    => Mage::helper('visitoripsecurity')->__('Last url'),
				'align'     => 'center',
				'width'     => '120px',
				'type'      => 'text',
				'default'   => '--',
				'index'     => 'last_url',
				'filter'    => false,
				'sortable'  => false,
				'renderer'  => 'visitoripsecurity/adminhtml_visitoripsecurity_renderer_lasturl'
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
					$field = new Zend_Db_Expr('lrn.note');
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
		return $this->getUrl('*/*/watch', array('_current'=>true));
	}


}