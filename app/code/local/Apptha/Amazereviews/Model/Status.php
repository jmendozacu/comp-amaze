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


class Apptha_Amazereviews_Model_Status extends Varien_Object
{
	const STATUS_PENDING    = 1;
    const STATUS_ENABLED	= 2;
    const STATUS_DISABLED	= 3;
    
//SETTING STATUS VALUES
    static public function getOptionArray()
    {
        return array(
            /*self::STATUS_PENDING    => Mage::helper('amazereviews')->__('Pending'),
            self::STATUS_ENABLED    => Mage::helper('amazereviews')->__('Accepted'),
            self::STATUS_DISABLED   => Mage::helper('amazereviews')->__('Denied'),
             */
            
        );
    }
}