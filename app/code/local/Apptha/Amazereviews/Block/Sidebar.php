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

class Apptha_Amazereviews_Block_Sidebar extends  Apptha_amazereviews_Block_Mostacceptedreviews
{
	// TRIMMING WORD COUNT FOR DISPLAY SIDEBAR REVIEW DETAIL

	protected function trim($str)
	{
			
		// GETTING MAX WORDS COUNT

		$max = Mage::getStoreConfig('amazereviews_section/sidebar/max_words');

		// TRIMMING REVIEW DETAIL

		if ($max > 0){
			$str = implode(' ', array_slice(preg_split('/\s+/', $str), 0, $max));                        

                }
		return $str;
	}

        // TRIMMING WORD COUNT TO DISPLAY SIDEBAR REVIEW TITLE

        protected function trimTitle($str)
	{

		// GETTING MAX WORDS COUNT

		$max = Mage::getStoreConfig('amazereviews_section/sidebar/title_words');

		// TRIMMING REVIEW DETAIL

		if ($max > 0){
			$str = implode(' ', array_slice(preg_split('/\s+/', $str), 0, $max));
		}
		return $str;
	}
        
	// GETTING REVIEWS FOR DISPLAY SIDEBAR

	public function getSidebarReviews($count)
	{
		// GETTING DUMMY CONTENT TITLE
		$review_title = Apptha_Amazereviews_Block_Reviewdetails_Averagerating :: getReviewTitle();
		
		// GETTING CURRENT PRODUCT ID

		$product=Mage::registry('current_product');
		$productId = $product->getId();

		// GETTING REVIEWS TO DISPLAY

	        $collection = Mage::getModel('review/review')->getResourceCollection()->setPageSize($count)
		->setCurPage(1)
		->addFieldToFilter('title', array('neq' => $review_title)); 
                $collection=$collection->addStoreFilter( Mage::app()->getStore()->getId() )
		->addEntityFilter('product', $productId)
		->addStatusFilter( Mage_Review_Model_Review::STATUS_APPROVED )
		->setDateOrder()
		->load()
		->addRateVotes()
		->addFieldToFilter('title', array('neq' => $review_title));		
		$collection=$collection->getItems();
	
	 //RETURN REVIEWS COLLECTION

     return $collection;
		
	}
		
		


}