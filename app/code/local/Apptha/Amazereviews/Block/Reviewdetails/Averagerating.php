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

class Apptha_Amazereviews_Block_Reviewdetails_Averagerating extends Mage_Review_Block_Form
{

	// GETTING TITLE SEPERATOR

	public function getTitleSeparator($store = null)
	{
		$separator = (string)Mage::getStoreConfig('catalog/seo/title_separator', $store);
		return ' ' . $separator . ' ';
	}

	// PREPARING LAYOUT

	protected function _prepareLayout()
	{
			
		return parent::_prepareLayout();
	}

	// GETTING CURRENT PRODUCT DATA

	public function getAdvancedProductData()
	{
		return  Mage::registry('current_product');
	}

	// GETTING RATES

	public function getRatings()
	{
		$ratingCollection = Mage::getModel('rating/rating')
		->getResourceCollection()
		->addEntityFilter('product')
		->setPositionOrder()
		->addRatingPerStoreName(Mage::app()->getStore()->getId())
		->setStoreFilter(Mage::app()->getStore()->getId())
		->load()
		->addOptionToItems();
		return $ratingCollection;
	}

	// GETTING REVIEW LIST URL

	public function getReviewlistUrl()
	{
		$productId = $this->getRequest()->getParam('id');
		$storeId = Mage::app()->getStore()->getId();

		return $this->helper('amazereviews/data')->getReviewUrl($storeId,$productId);
	}

	// SAVING STAR RATING

	public function saveAdvancedRatings()
	{
		return Mage::getUrl('amazereviews/index/post');

	}


	// CHECK WHETHER CUSTOMER RATED OR NOT

	public function checkVote($product_id,$customer_id)
	{	
		$review_title=$this->htmlEscape($this->getReviewTitle());
	
		$items = Mage::getModel('review/review')
		->getResourceCollection()
		//->addFieldToFilter('customer_id', array('eq' => $customer_id))	
		//->addFieldToFilter('title', array('neq' => $review_title))
		->addStoreFilter(Mage::app()->getStore()->getId())
		->addEntityFilter('product', $product_id)		
		->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)	
		->setDateOrder()
		->addRateVotes();
		$ratings = array();

		if($items)
		{
			foreach($items as $review)
			{				
				if($review->getTitle()==$review_title && $review->getCustomerId()==$customer_id)
				{
					$ratings= $review;	
				}
			}
				
			
		}	
	
		return $ratings;
	}	
	
// CHECK WHETHER CUSTOMER RATED OR NOT IN PENDING LIST

	public function checkVotePending($product_id,$customer_id)
	{	
		$review_title=$this->htmlEscape($this->getReviewTitle());
	
		$items = Mage::getModel('review/review')
		->getResourceCollection()
		//->addFieldToFilter('customer_id', array('eq' => $customer_id))	
		//->addFieldToFilter('title', array('neq' => $review_title))
		->addStoreFilter(Mage::app()->getStore()->getId())
		->addEntityFilter('product', $product_id)		
		->addStatusFilter(Mage_Review_Model_Review::STATUS_PENDING)	
		->setDateOrder()
		->addRateVotes();	
		

		$ratings_pending = array();

		if($items)
		{
			foreach($items as $review)
			{				
				if($review->getTitle()==$review_title && $review->getCustomerId()==$customer_id)
				{
					$ratings_pending= $review;	
				}
			}
				
			
		}	
	
		return $ratings_pending;
	}

	// GETTING ALL REVIEWS COLLECTION

	public function allReviews($productId)
	{
		// REVIEW COLLECTION
		
		
		$review_title = $this->htmlEscape(Apptha_Amazereviews_Block_Reviewdetails_Averagerating::getReviewTitle());

		$reviews = Mage::getModel('review/review')
		->getResourceCollection()
		->addFieldToFilter('title', array('neq' => $review_title))
		->addStoreFilter(Mage::app()->getStore()->getId())
		->addEntityFilter('product', $productId)
		->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
		
		->setDateOrder()
		->addRateVotes();	
		

		return $reviews;

	}

	// CALCULATING AVERAGE RATINGS

	public function averageRatings($productId)
	{

		// REVIEW COLLECTION

		$reviews = Mage::getModel('review/review')
		->getResourceCollection()
		->addStoreFilter(Mage::app()->getStore()->getId())
		->addEntityFilter('product', $productId)
		->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
		->setDateOrder()
		->addRateVotes();

		// RATINGS

		$ratings = array();

		if (count($reviews) > 0) {
			foreach ($reviews->getItems() as $review) {
				foreach( $review->getRatingVotes() as $vote ) {

					$ratings[] = $vote->getPercent();

				}
                            }
			$count=count($ratings);
			$avg = array_sum($ratings)/$count;
		}
		return round($avg,1);
	}

	public function totalRatings($productId)
	{		
		$reviews = Mage::getModel('review/review')
		->getResourceCollection()		
		->addStoreFilter(Mage::app()->getStore()->getId())
		->addEntityFilter('product', $productId)
		->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)		
		->setDateOrder()
		->addRateVotes();

		// RATINGS

		$ratings = array();

		if (count($reviews) > 0) {
			foreach ($reviews->getItems() as $review) {
				foreach( $review->getRatingVotes() as $vote ) {

					$ratings[] = $vote->getPercent();

				}
			}


		}

		return $ratings;
	}

	// ASSIGNING DUMMY TITLE

	public function getReviewTitle()
	{
		
		return $this->__('My Review');

	}

	// ASSIGNING DUMMY REVIEW DETAIL

	public function getReviewDetails()
	{
		return $this->__('I have rated this product');

	}		

}
