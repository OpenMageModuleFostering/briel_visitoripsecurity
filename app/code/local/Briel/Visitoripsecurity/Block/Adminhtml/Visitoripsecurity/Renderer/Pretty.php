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
class Briel_Visitoripsecurity_Block_Adminhtml_Visitoripsecurity_Renderer_Pretty extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		
		$url = $row->_data['url'];
		$base_url = Mage::getBaseUrl();
		
		$url = str_ireplace($base_url, '', $url);
		
		$collection = Mage::getModel('core/url_rewrite')->getCollection();
		
		$select = $collection->getSelect();
		$select->where("main_table.target_path LIKE '".$url."'");
		
		//$collection->printLogQuery(true);
		
		$arrData = $collection->getData();
		
		
		if(!empty($arrData)){
			foreach ($arrData as $k => $v){
				if(!empty($v['request_path'])){
					$pretty = $base_url.$v['request_path'];
					break;
				}
			}
		}
		if(!empty($pretty)){
			echo $pretty;
		}else{
			echo $row->_data['url'];
		}

	}
}