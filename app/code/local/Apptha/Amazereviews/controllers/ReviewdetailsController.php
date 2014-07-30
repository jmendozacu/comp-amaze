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

class Apptha_Amazereviews_ReviewdetailsController extends Mage_Core_Controller_Front_Action
{
	/**
	 * Action predispatch
	 *
	 * Check customer authentication for some actions
	 */
	public function preDispatch()
	{
		parent::preDispatch();
		if (!Mage::getSingleton('customer/session')->authenticate($this)) {
			$this->setFlag('', self::FLAG_NO_DISPATCH, true);
		}
	}

	public function indexAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}

	//FOCUS CURSOR ON HEADER BLOCK

	public function viewAction()
	{
		$this->loadLayout();
		if ($navigationBlock = $this->getLayout()->getBlock('customer_account_navigation')) {
			$navigationBlock->setActive('review/customer');
		}
		$this->getLayout()->getBlock('head')->setTitle($this->__('Review Details'));
		$this->renderLayout();
	}
}
