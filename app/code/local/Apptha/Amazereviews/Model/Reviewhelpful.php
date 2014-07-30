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


class Apptha_Amazereviews_Model_Reviewhelpful extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
       
        $this->_init('amazereviews/reviewhelpful');
    }

    //FUNCTION FOR ACCEPTED REVIEWS

      public function getAccepted()
      {
         //GETTING CUSTOMER ID

      	$customer = Mage::getSingleton('customer/session')->getCustomer();
      	$customerId = $customer->getId();
     
      	//GETTING VALUE FROM URL

      	$value=Mage::app()->getRequest()->getParam('value');

        //GETTING PRODUCT ID FROM URL
      
      	$productId = Mage::app()->getRequest()->getParam('productid');
     
      	//GETTTING REVIEW ID FROM URL

    	$reviewId = Mage::app()->getRequest()->getParam('reviewid');
    	
      
      	if($value==1)
      	{
       //RETRIVE EXISTING COUNTER_YES VALUE AND INCREMENT BY 1
       
      	 $reviews = Mage::getModel('review/review')->load($reviewId);
      	 $counterYes = $reviews->getCounterYes();
         $createdAt = $reviews->getCreatedAt();
         $counterYes = $counterYes + 1 ;
       
    	 //UPDATE THE COUNTER_YES VFIELD WITH INCREMENTED VALUE


         // ASSIGN TABLE PREFIX IF IT'S EXIST

         $table_name= Mage::getSingleton('core/resource')->getTableName('review');    
         

    	 $connection = Mage::getSingleton('core/resource')
		->getConnection('core_write');
		$connection->beginTransaction();
		$fields = array();
		$fields['counter_yes'] = $counterYes;
                 $fields['created_at']  = $createdAt;
		$where = $connection->quoteInto('review_id =?', $reviewId);
		$connection->update($table_name, $fields, $where);
		$connection->commit();	

		//INSERT ACCEPTED REVIEW DATA INTO REVIEW HELPFUL TABLE
    	
      	 $collection = Mage::getModel('amazereviews/reviewhelpful');
    	 $data = array('customerid'=>$customerId,'product_id'=>$productId,'reviewid'=>$reviewId,'accepted_status'=>'Yes');
    	 $collection->setData($data);    	 
    	 $collection->save();
    	 
    	 
      	}
      	else
      	 {
      	  //RETRIVE EXISTING COUNTER_NO VALUE AND INCREMENT BY 1
      	 
      	 	$reviewId = Mage::app()->getRequest()->getParam('reviewid');
    	$reviews = Mage::getModel('review/review')->load($reviewId);
    	$counterNo = $reviews->getCounterNo();
        $createdAt = $reviews->getCreatedAt();
    	$counterNo = $counterNo + 1 ;      	
     	 
    	//UPDATE THE COUNTER_nO VFIELD WITH INCREMENTED VALUE


                // ASSIGN TABLE PREFIX IF IT'S EXIST

       $table_name= Mage::getSingleton('core/resource')->getTableName('review');    
    	
    	$connection = Mage::getSingleton('core/resource')
		->getConnection('core_write');
		$connection->beginTransaction();
		$fields = array();
		$fields['counter_no'] = $counterNo;
                $fields['created_at']  = $createdAt;
		$where = $connection->quoteInto('review_id =?', $reviewId);
		$connection->update($table_name, $fields, $where);
		$connection->commit();

		//INSERT NOT ACCEPTED REVIEW DATA INTO REVIEW HELPFUL TABLE
		
      	 $collection = Mage::getModel('amazereviews/reviewhelpful');
    	 $data = array('customerid'=>$customerId,'product_id'=>$productId,'reviewid'=>$reviewId,'accepted_status'=>'No');
    	 $collection->setData($data);    	 
    	 $collection->save();
      	 }
      	
      }
      
      public function getDeleted()
      {
      	$reviewId = Mage::app()->getRequest()->getParam('reviewid');
      	
      	 $collection = Mage::getModel('amazereviews/reviewhelpful');
      	 $collection->setId($reviewId)->delete();         	 
      	
      }
      
   
}


