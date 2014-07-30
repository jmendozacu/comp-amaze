<?php

/**
 * Apptha
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.apptha.com/LICENSE.txt
 *
 * ==============================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * ==============================================================
 * This package designed for Magento COMMUNITY edition
 * Apptha does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Apptha does not provide extension support in case of
 * incorrect edition usage.
 * ==============================================================
 *
 * @category    Apptha
 * @package     Apptha_Amazereviews
 * @version     0.2.2
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 **/
class Apptha_Amazereviews_Block_Adminhtml_Amazereviews extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
  	// TELL MAGENTO THE LOCATION OF OUR GRID.PHP FILE
    $this->_controller = 'adminhtml_amazereviews';
    $this->_blockGroup = 'amazereviews';
    //HEADER TEXT TO DISPLAY IN THE TOP OF THE GRID
    $this->_headerText = Mage::helper('amazereviews')->__('Abused Reviews List');
    //TO DISPLAY ADD ITEM BUTTON
    $this->_addButtonLabel = Mage::helper('amazereviews')->__('Add Item');
    parent::__construct();
    // TO REMOVE ADD ITEM BUTTON IN TOP RIGHT OF THE GRID
    $this->_removeButton('add');
   }
}