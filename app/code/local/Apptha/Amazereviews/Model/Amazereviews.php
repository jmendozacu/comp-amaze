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


class Apptha_Amazereviews_Model_Amazereviews extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('amazereviews/amazereviews');
    }

    //FUNTION FOR REPORT ABUSE
    
      public function getUpdateReportabuse($reviewId,$txtMsg)
      {     	
      	//GETTING CUSTOMER EMAIL ID
      	
    	$customer = Mage::getSingleton('customer/session')->getCustomer();
    	$customerEmail = $customer->getEmail();

        //GETTING CUSTOMER NAME
    	  	
    	$customerName = Mage::helper('customer')->getCustomerName();
    	
    	 //GETTING CUSTOMER REVIEW DETAILS
    	
    	$review = Mage::getModel('review/review')->load($reviewId);
       	$reviewcontent = $review->getDetail();
    	
    	
    	$comment =$txtMsg;

        //GETTING PRODUCT ID
    	  	
    	$review = Mage::getModel('review/review')->load($reviewId);
    	$productId = $review->getEntityPkValue();

        //GETTING PRODUCT NAME
    	
    	$_product = Mage::getModel('catalog/product')->load($productId);
    	$productName = $_product->getName(); 

    	$max = Mage::getStoreConfig('amazereviews_section/autodelete_abusedreviews/count');
       	
    	 //RETRIVE EXISTING COUNTER_YES VALUE AND INCREMENT BY 1
      	 $reviews = Mage::getModel('review/review')->load($reviewId);
    	 $abusecounter = $reviews->getAbuseCounter();
         $createdAt = $reviews->getCreatedAt();
    
    	 if($abusecounter==$max)
    	 {
    	 	$Id = Mage::app()->getRequest()->getParam('reviewid');
   			$coreResource = Mage::getSingleton('core/resource');
   			$conn = $coreResource->getConnection('core_read');
   			$conn->delete($coreResource->getTableName('review/review'),"review_id=$Id");
    	 
    	 }
    	 else
    	 {
    	 $abusecounter = $abusecounter + 1 ;
       //UPDATE THE COUNTER_YES VFIELD WITH INCREMENTED VALUE


          // ASSIGN TABLE PREFIX IF IT'S EXIST

    $table_name= Mage::getSingleton('core/resource')->getTableName('review');    


         $connection = Mage::getSingleton('core/resource')
		->getConnection('core_write');
		$connection->beginTransaction();
		$fields = array();
		$fields['abuse_counter'] = $abusecounter;
                $fields['created_at']  = $createdAt;
		$where = $connection->quoteInto('review_id =?', $reviewId);
		$connection->update($table_name, $fields, $where);
		$connection->commit();

         //INSERT QUERY TO STORE THE ABUSE REVIEW DETIALS IN ADVANCEDREVIEW TABLE
    	
    	 $collection = Mage::getModel('amazereviews/amazereviews');
    	 $data = array('comment'=>$comment,'review'=>$reviewcontent,'customer'=>$customerName,'status'=>1,'product'=>$productName,'email'=>$customerEmail,'review_id'=>$reviewId,'product_id'=>$productId);
    	 $collection->setData($data);    	 
    	 $collection->save();  

  if(Mage::getStoreConfig('amazereviews_section/autodelete_abusedreviews/active')==1)
   { 
  $max = Mage::getStoreConfig('amazereviews_section/autodelete_abusedreviews/count');
    	 
    	 if($abusecounter>=$max)
    	 {    	 
			
		$model = Mage::getModel('review/review');
		try
		{
			// SET SECURE ADMIN AREA
				
			Mage::register('isSecureArea', true);
			$model->setId($reviewId)->delete();
				
			// UN SET SECURE ADMIN AREA
			Mage::unregister('isSecureArea');
				
		} catch (Exception $e){
			echo $e->getMessage();
		}
		 
		//  MESSAGE FOR REVIEW DELETED SUCCESSFULLY
		//Mage::getSingleton('core/session')->addSuccess("Your review has been deleted! ");
		$this->_redirectUrl($this->getRequest()->getServer('HTTP_REFERER'));
    	 	
    	 }	
    	 	
    }
    	 	
    }
      	
    
      }
      
      

}