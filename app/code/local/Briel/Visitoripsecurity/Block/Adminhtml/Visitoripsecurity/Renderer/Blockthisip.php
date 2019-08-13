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
class Briel_Visitoripsecurity_Block_Adminhtml_Visitoripsecurity_Renderer_Blockthisip extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{
	public function render(Varien_Object $row)
	{
		
		if(!Mage::helper('visitoripsecurity')->checkIfBlocked($row->_data['remote_addr'])){
			if(!Mage::helper('visitoripsecurity')->checkIfWhite($row->_data['remote_addr'])){
				$html = '<button onclick="blockThisIp(this, '. $row->_data['remote_addr'] .'); return false">' . Mage::helper('visitoripsecurity')->__('Block') . '</button>';
			}else{
				$html = '';
			}
		}else{
			$html = '<button onclick="unBlockThisIp(this, '. $row->_data['remote_addr'] .'); return false">' . Mage::helper('visitoripsecurity')->__('UnBlock') . '</button>';
		}
		
        return $html;
	}
	
}