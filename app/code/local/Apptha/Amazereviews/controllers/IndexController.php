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
class Apptha_Amazereviews_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
		$this->loadLayout();
		$this->renderLayout();

	}

	public function postAction()
	{
		$advanced_rate = $this->getRequest()->getParam('advanced_rate');
		$customer_id = $this->getRequest()->getParam('customer_id');
		$product_id = $this->getRequest()->getParam('product_id');

		$collection = Mage::getModel('amazereviews/advancedratings');


		$data = array('product_id'=> $product_id,'customer_id'=>$customer_id ,'advanced_rate'=>$advanced_rate);
		$collection->setData($data);
		$collection->save();

		Mage::getSingleton('core/session')->addSuccess('Thanks for your rating, you rated '.$advanced_rate.' for this product! ');
			
		$this->_redirectReferer();

			
	}

	//CONTROLLER FUNCTIN TO ABUSE A REVIEW

	public function reportAction()
	{
		//GET THE REVIEW ID FROM URL

		$reviewid = $this->getRequest()->getParam('reviewid');

		//CALL THE METHOD IN MODEL BY PASSING REVIEW ID
		 
		Mage::getModel('amazereviews/amazereviews')->getReportabuse($reviewid);

	}
	public function updatereportAction()
	{
		$reviewid = $this->getRequest()->getPost('review_id');
		$txtMsg = $this->getRequest()->getPost('txtMsg');

		Mage::getModel('amazereviews/amazereviews')->getUpdateReportabuse($reviewid,$txtMsg);
	}

	//DISPLAY REVIEW IN SEPARATE PAGE

	public function viewAction()
	{
		//GET THE REVIEW ID FROM URL

		$reviewid = $this->getRequest()->getParam('reviewid');

		//CALL THE METHOD IN MODEL BY PASSING REVIEW ID

		$review = Mage::getModel('review/review')->load($reviewid);
		$this->loadLayout();
		$this->renderLayout();
			
	}

	//CONTROLLER ACTION FOR CLICKING YES IN 'WAS THIS REVIEW HELPFUL?'

	public function acceptedyesAction()
	{
		//GET THE REVIEW ID FROM URL

		$reviewid = $this->getRequest()->getParam('reviewid');

		//CALL THE METHOD IN MODEL BY PASSING REVIEW ID

		Mage::getModel('amazereviews/reviewhelpful')->getAccepted($reviewid);

		//SUCCESS MESSAGE

                $message=$this->__('Thanks for your vote!');
		echo json_encode(array('success'=>true,'error'=>false,'message'=>$message));
		exit ;

			
	}

	//CONTROLLER ACTION FOR CLICKING NO IN 'WAS THIS REVIEW HELPFUL?'

	public function acceptednoAction()
	{
		//GET THE REVIEW ID FROM URL

		$reviewid = $this->getRequest()->getParam('reviewid');

		//CALL THE METHOD IN MODEL BY PASSING REVIEW ID

		Mage::getModel('amazereviews/reviewhelpful')->getAccepted($reviewid);

		//SUCCESS MESSAGE
                $message=$this->__('Thanks for your vote!');
		echo json_encode(array('success'=>true,'error'=>false,'message'=>$message));
		exit ;
			
			
	}

	//CONTROLLER ACTION TO DELETE REVIEW

	public function deletereviewAction()
	{
		$reviewid = $this->getRequest()->getParam('reviewid');
			
		$model = Mage::getModel('review/review');
		try
		{
			// SET SECURE ADMIN AREA
				
			Mage::register('isSecureArea', true);
			$model->setId($reviewid)->delete();
				
			// UN SET SECURE ADMIN AREA
			Mage::unregister('isSecureArea');
				
		} catch (Exception $e){
			echo $e->getMessage();
		}
		 
		//  MESSAGE FOR REVIEW DELETED SUCCESSFULLY
		$deletereview=$this->__('Your review has been deleted!');
                Mage::getSingleton('core/session')->addSuccess($deletereview);
		$this->_redirectUrl($this->getRequest()->getServer('HTTP_REFERER'));
	}

}