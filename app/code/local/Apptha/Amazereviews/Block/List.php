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


class Apptha_Amazereviews_Block_List extends Mage_Core_Block_Template
{
      /**
     * Retrieve HTML title value separator (with space)
     *
     * @param mixed $store
     * @return string
     */
	

	// GETTING A SPECIFIC REVIEW DETAILS
    
    public function getReviewitem($reviewid)
    {
    	
    $review = Mage::getModel('review/review')->load($reviewid);
    	
   	return $review;    
    }
	
    
    // GETTING A SPECIFIC REVIEW RATINGS DETAIL
    
public function getRatingsitem($reviewid)
    {  
       
       $ratingCollection = Mage::getModel('rating/rating_option_vote')
                ->getResourceCollection()
                ->setReviewFilter($reviewid)
                ->setStoreFilter(Mage::app()->getStore()->getId())
                ->addRatingInfo(Mage::app()->getStore()->getId())
                ->load()
                ->getItems();          
              
          return $ratingCollection;            
              
                
    }    
    
}
