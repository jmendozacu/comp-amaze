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


class Apptha_Amazereviews_Adminhtml_AmazereviewsController extends Mage_Adminhtml_Controller_action
{
	//SHOWS THE GRID

	protected function _initAction() {
		$this->loadLayout()

		//FUNCTION IS TO ADD THE BREADCRUMBS TO DISPLAY
		->_setActiveMenu('amazereviews/items')
		->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));

		return $this;
	}


	//INDEXACTION IS THE DEFAULT ACTION FOR ANY CONTROLLER

	public function indexAction() {
		$this->_initAction()
		->renderLayout();
	}

	//SHOWS THE EDIT/NEW FORM

	public function editAction() {

		//LOAD CORRESPONDING ID FROM URL

		$id     = $this->getRequest()->getParam('id');

		//DATA COLLECTION FROM amazereviews TABLE FOR PARTICULAR ID

		$model  = Mage::getModel('amazereviews/amazereviews')->load($id);

		//DATA FOR THAT PARTICULAR ID WILL BE DISPLAYED IN A FORM, WE CAN EDIT THIS FORM DATA

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('amazereviews_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('amazereviews/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('amazereviews/adminhtml_amazereviews_edit'))
			->_addLeft($this->getLayout()->createBlock('amazereviews/adminhtml_amazereviews_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amazereviews')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}



	public function newAction() {
		$this->_forward('edit');
	}

	//SAVES THE FORM DATA

	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
				
			if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
				try {
					/* Starting upload */
					$uploader = new Varien_File_Uploader('filename');
						
					// Any extention would work
					$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
						
					// Set the file upload mode
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
						
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . DS ;
					$uploader->save($path, $_FILES['filename']['name'] );
						
				} catch (Exception $e) {

				}
	    
				//this way the name is saved in DB
				$data['filename'] = $_FILES['filename']['name'];
			}


			$model = Mage::getModel('amazereviews/amazereviews');
			$model->setData($data)
			->setId($this->getRequest()->getParam('id'));
				
			try {

				//TO STORE THE CREATED TIME AND DATE

				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
					->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}

				$model->save();

				// TO DISPLAY SUCCESS MESSAGE

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('amazereviews')->__('Item has been saved successfully'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				//IF CLICK BACK BUTTON PAGE WILL REDIRECT TO PREVIOUS PAGE

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amazereviews')->__('Unable to find item to save'));
		$this->_redirect('*/*/');
	}

	//DELETE DATA FOR PARTICULAR ID

	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {

				//DATA COLLECTION FORM ADVANCEDRECIEWS TABLE

				$model = Mage::getModel('amazereviews/amazereviews');
					
				//SELECTED ID'S DATA WILL BE DELETED

				$model->setId($this->getRequest()->getParam('id'))
				->delete();
					
				//SUCCESS MESSAGE

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item has been deleted successfully'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

	//DELETE DATA FOR SELECTED ROW ID

	public function massDeleteAction() {
		 
		//STORE THE SELECTED ROW ID'S IN A VARIBLE
		 
		$amazereviewsIds = $this->getRequest()->getParam('amazereviews');

		//CHECK CONDITION IF NO ROW IS SELECTED

		if(!is_array($amazereviewsIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
		} else {
			try {


                               // ASSIGN TABLE PREFIX IF IT'S EXIST
                               $table_name= Mage::getSingleton('core/resource')->getTableName('review');        
				 
				//LOAD THE SELECTED ROW ID TO DELETE

                                //UPDATE DATA FROM THE ABUSE COUNTER IN REVIEW TABLE
				 
				foreach ($amazereviewsIds as $amazereviewsId)
                                 {
					$amazereviews = Mage::getModel('amazereviews/amazereviews')->load($amazereviewsId);
					$amazereviews->delete();
                                        $reviewId=$amazereviews->getReviewId();
					$abuse = Mage::getModel('review/review')->load($reviewId);
					$abuseCount = $abuse->getAbuseCounter();
                                        $abuseCount = $abuseCount - 1 ;
                                        $connection = Mage::getSingleton('core/resource')
					->getConnection('core_write');
					$connection->beginTransaction();
					$fields = array();
					$fields['abuse_counter'] = $abuseCount;
					$where = $connection->quoteInto('review_id =?', $reviewId);
					$connection->update($table_name, $fields, $where);
					$connection->commit();
				}

				//SUCCESS MESSAGE
				Mage::getSingleton('adminhtml/session')->addSuccess(
				Mage::helper('adminhtml')->__(
                        'Total of %d record(s) have been deleted successfully', count($amazereviewsIds)
				)
				);
			} catch (Exception $e) {				 
				//ERROR MESSAGE				 
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}

	//CHANGE STATUS DATA FOR SELECTED ROW ID

	public function massStatusAction()
	{
		//STORE THE SELECTED ROW ID'S IN A VARIBLE
		 
		$amazereviewsIds = $this->getRequest()->getParam('amazereviews');

		//CHECK CONDITION IF NO ROW IS SELECTED
		 
		if(!is_array($amazereviewsIds)) {
			Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
		} else {
			try {
				 
				//LOAD THE SELECTED ROW ID TO SAVE
				 
				foreach ($amazereviewsIds as $amazereviewsId) {
					$amazereviews = Mage::getSingleton('amazereviews/amazereviews')
					->load($amazereviewsId)
					->setStatus($this->getRequest()->getParam('status'))
					->setIsMassupdate(true)
					->save();
					 
				}

				//SUCCESS MESSAGE

				$this->_getSession()->addSuccess(
				$this->__('Total of %d record(s) have been updated successfully', count($amazereviewsIds))
				);
			} catch (Exception $e) {
				 
				//ERROR MESSAGE
				 
				$this->_getSession()->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}

	//FUNCTION TO EXPORT REPORT AS CSV FILE

	public function exportCsvAction()
	{
		//GIVE FILE NAME FOR EXPORTED CSV FILE
		 
		$fileName   = 'Abuselist.csv';
		$content    = $this->getLayout()->createBlock('amazereviews/adminhtml_amazereviews_grid')
		->getCsv();

		$this->_sendUploadResponse($fileName, $content);
	}

	//FUNCTION TO EXPORT REPORT AS XML FILE

	public function exportXmlAction()
	{
		//GIVE FILE NAME FOR EXPORTED XML FILE
		 
		$fileName   = 'Abuselist.xml';
		$content    = $this->getLayout()->createBlock('amazereviews/adminhtml_amazereviews_grid')
		->getXml();

		$this->_sendUploadResponse($fileName, $content);
	}

	//FUNCTION TO EXPORT XML AND CSV FILES

	protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
	{
		$response = $this->getResponse();
		$response->setHeader('HTTP/1.1 200 OK','');
		$response->setHeader('Pragma', 'public', true);
		$response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
		$response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
		$response->setHeader('Last-Modified', date('r'));
		$response->setHeader('Accept-Ranges', 'bytes');
		$response->setHeader('Content-Length', strlen($content));
		$response->setHeader('Content-type', $contentType);
		$response->setBody($content);
		$response->sendResponse();
		die;
	}




}