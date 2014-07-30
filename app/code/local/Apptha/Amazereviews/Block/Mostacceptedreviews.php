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



class Apptha_Amazereviews_Block_Mostacceptedreviews extends Mage_Review_Block_Product_View_List
{

	// PREPARING LAYOUT

	protected function _prepareLayout()
	{
		parent::_prepareLayout();

		if ($toolbar = $this->getLayout()->getBlock('product_review_list.toolbar')) {
			$toolbar->setCollection($this->getReviews());
			$this->setChild('toolbar', $toolbar);
		}

		return $this;
	}


	// GETTING REPORT ABUSE URL

	public function getReportabuseUrl($reviewid)
	{
			
		return $this->getBaseUrl().'amazereviews/index/report/reviewid/'.$reviewid;		
			
	}
	
	// GETTING REPORT ABUSE URL

	public function getUpdateReportabuseUrl()
	{
			
		return $this->getBaseUrl().'amazereviews/index/updatereport/';		
			
	}
	

	// GETTING PERMA LINK URL

	public function getPermaUrl($reviewid)
	{
			
		$productId = $this->getRequest()->getParam('id');
		$storeId = Mage::app()->getStore()->getId();

		return $this->helper('amazereviews/data')->getPermaReviewUrl($storeId,$productId, $reviewid);

	}

	// GETTING REVIEW URL

	public function getReviewUrl($reviewid)
	{
		$productId = $this->getRequest()->getParam('id');
		$storeId = Mage::app()->getStore()->getId();

		return $this->getBaseUrl().'amazereviews/index/view/reviewid/'.$reviewid.'/productid/'.$productId;
			
	}


	// REDIRECT ACCEPTED REVIEW URL

     public function getAcceptedYesUrl($reviewid,$productId)
    {
    	
    	return $this->getBaseUrl().'amazereviews/index/acceptedyes/reviewid/'.$reviewid.'/productid/'.$productId.'/value/1';
    	
    }
    
  public function deleteReviewUrl($reviewid)
    {
    	
    	return $this->getBaseUrl().'amazereviews/index/deletereview/reviewid/'.$reviewid;
    	
    }
    
    
    
    // REDIRECT ACCEPTED REVIEW URL
    
    public function getAcceptedNoUrl($reviewid,$productId)
    {
    	
    	return $this->getBaseUrl().'amazereviews/index/acceptedno/reviewid/'.$reviewid.'/productid/'.$productId.'/value/0';
    	
    }   

	// CHECKING WHETHER CUSTOMER ABUSED REVIEW OR NOT

	public function getCheckFlag($customer_mail,$review_id)
	{


		$coreResource = Mage::getSingleton('core/resource');

		$conn = $coreResource->getConnection('core_read');

		$table = $coreResource->getTableName('amazereviews/amazereviews');
		//$prefix=Mage::getConfig()->getTablePrefix();
		//$table=$prefix.$table;

		$select = $conn->select()
		->from(array('p' => $table ), new Zend_Db_Expr('abuse_id'))
		->where('review_id = ?',$review_id )
		->where('email = ?', $customer_mail );

	 $count = $conn->fetchOne($select);

	 return $count;

	  
	  
	}


	// GETTING REVIEWS TO DISPLAY MOST ACCEPTED PAGE

	public function getReviews() {
		$review_title = Apptha_Amazereviews_Block_Reviewdetails_Averagerating :: getReviewTitle();
		if(Mage::registry('current_product'))
		{
			$product=Mage::registry('current_product');
			$productId = $product->getId();
			$currPage = Mage::app()->getRequest()->getParam('p', 1);
			$pageSize = Mage::app()->getRequest()->getParam('limit', 10);
			$reviews = Mage::getModel('review/review')->getResourceCollection()->setPageSize($pageSize)
			->setCurPage($currPage)
			->addFieldToFilter('title', array('neq' => $review_title));
		}
		else
		{
			$reviews = Mage::getModel('review/review')->getResourceCollection()->addFieldToFilter('title', array('neq' => $review_title));
			
		}

		$reviews->addStoreFilter( Mage::app()->getStore()->getId() )
		->addEntityFilter('product', $productId)
		->addStatusFilter( Mage_Review_Model_Review::STATUS_APPROVED )
		
		->setOrder('counter_yes')
		->setDateOrder('Desc')
		->load()
			
		->addRateVotes();		

		return $reviews;
	}
	
	
// CHECKING WHETHER CUSTOMER VOTED A REVIEW OR NOT

	public function getCheckVote($customer_id,$review_id)
	{
				
		$coreResource = Mage::getSingleton('core/resource');

		$conn = $coreResource->getConnection('core_read');

		$table = $coreResource->getTableName('amazereviews/reviewhelpful');
		//$prefix=Mage::getConfig()->getTablePrefix();
		//$table=$prefix.$table;

		$select = $conn->select()
		->from(array('p' => $table ), new Zend_Db_Expr('accepted_id'))
		->where('reviewid = ?',$review_id )
		->where('customerid = ?', $customer_id )
		->where('accepted_status IN(?)', array(0,1));

	 $count = $conn->fetchOne($select);

       	 
	 return $count;

	  
	  
	}

}