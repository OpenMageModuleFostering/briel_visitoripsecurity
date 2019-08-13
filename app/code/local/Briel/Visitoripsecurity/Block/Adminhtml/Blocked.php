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
class Briel_Visitoripsecurity_Block_Adminhtml_Blocked extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		
		$this->_controller = 'adminhtml_visitoripsecurity_blocked';
		$this->_blockGroup = 'visitoripsecurity';
		$this->_headerText = Mage::helper('visitoripsecurity')->__('Blocked ip list');
		
		
		$this->_addButton("view_list", array('label' => Mage::helper('visitoripsecurity')->__('View list'),
				'onclick' => "setLocation('".$this->getUrl('*/*/index')."')"));
		$this->_addButton("view_blocked", array('label' => Mage::helper('visitoripsecurity')->__('View blocked'), 
												'onclick' => "setLocation('".$this->getUrl('*/*/blocked')."')"));
		$this->_addButton("view_white", array('label' => Mage::helper('visitoripsecurity')->__('View white list'),
				'onclick' => "setLocation('".$this->getUrl('*/*/white')."')"));
		$this->_addButton("view_watch", array('label' => Mage::helper('visitoripsecurity')->__('View watch list'),
				'onclick' => "setLocation('".$this->getUrl('*/*/watch')."')"));
		$this->_addButton("one_ip", array('label' => Mage::helper('visitoripsecurity')->__('Block ip classes'),
				'onclick' => "setLocation('".$this->getUrl('*/*/oneip')."')"));
		
		
		parent::__construct();
		$this->removeButton('add');
		
	}
}