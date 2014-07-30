
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

class Apptha_Amazereviews_Block_Amazereviews extends Mage_Review_Block_Product_View_List
{



	// PREPARE LAYOUT

	public function _prepareLayout()
	{
		parent::_prepareLayout();

		if ($toolbar = $this->getLayout()->getBlock('product_review_list.toolbar')) {
			$toolbar->setCollection($this->getAllReviews($this->getProductId()));
			$this->setChild('toolbar', $toolbar);
		}

		return $this;
	}

	// GETTING ADVANCED REVIEWS

	public function getamazereviews()
	{
		if (!$this->hasData('amazereviews')) {
			$this->setData('amazereviews', Mage::registry('amazereviews'));
		}
		return $this->getData('amazereviews');

	}

	// GETTING PRODUCT ID

	public function getProductId()
	{
		return Mage::app()->getRequest()->getParam('id', false);
	}

	// GETTING ALL REVIEWS FOR A PRODUCT
	public function getAllReviews($productId)
	{

		$review_title = Apptha_amazereviews_Block_Reviewdetails_Averagerating :: getReviewTitle();

		$currPage = Mage::app()->getRequest()->getParam('p', 1);
		$pageSize = Mage::app()->getRequest()->getParam('limit', 10);
		$reviews = Mage::getModel('review/review')->getResourceCollection()
		->setPageSize($pageSize)
		->setCurPage($currPage)
		->addFieldToFilter('title', array('neq' => $review_title));


		$reviews->addStoreFilter( Mage::app()->getStore()->getId() )
		->addEntityFilter('product', $productId)
		->addStatusFilter( Mage_Review_Model_Review::STATUS_APPROVED )
		->setDateOrder()
		->load()
		
		->addRateVotes();

		return $reviews;

	}

}