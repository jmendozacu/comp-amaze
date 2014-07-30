<?php

/**
 * Iksanika llc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.iksanika.com/products/IKS-LICENSE.txt
 *
 * @category   Iksanika
 * @package    Iksanika_Productupdater
 * @copyright  Copyright (c) 2013 Iksanika llc. (http://www.iksanika.com)
 * @license    http://www.iksanika.com/products/IKS-LICENSE.txt
 */

include_once "Mage/Adminhtml/controllers/Catalog/ProductController.php";

class Iksanika_Productupdater_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController
{

    protected $massactionEventDispatchEnabled = true;
    public static $exportFileName = 'products';
    
    
    protected function _construct()
    {
        // Define module dependent translate
        $this->setUsedModuleName('Iksanika_Productupdater');
    }
    
    /**
     * Product list page
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('catalog/productupdater');
        $this->_addContent($this->getLayout()->createBlock('productupdater/catalog_product'));
        $this->renderLayout();
    }

    /**
     * Product grid for AJAX request
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('productupdater/catalog_product_grid')->toHtml()
        );
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/products');
    }
    
    public function massRefreshProductsAction()
    {
        $productIds = $this->getRequest()->getParam('product');
        if (is_array($productIds)) 
        {
            try {
                foreach ($productIds as $productId) 
                {
                    $product = Mage::getModel('catalog/product')->load($productId);
                    if ($this->massactionEventDispatchEnabled)
                    {
                        Mage::dispatchEvent('catalog_product_prepare_save', array('product' => $product, 'request' => $this->getRequest()));
                    }
                    $product->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully refreshed.', count($productIds)));
            } catch (Exception $e) 
            {
                $this->_getSession()->addError($e->getMessage());
            }
        }else
        {
            $this->_getSession()->addError($this->__('Please select product(s)'));
        }
        $this->_redirect('*/*/index', array('_current' => true));
    }

    public function massUpdateProductsAction()
    {
        $productIds = $this->getRequest()->getParam('product');

        if (is_array($productIds)) 
        {
            try {
                $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
                                ->addVisibleFilter()
                                ->addStoreLabel(Mage::helper('productupdater')->getStoreId());
                $attributes->getSelect();
                
                $attrs    =   array();
                foreach($attributes as $attribute)
                {
                    $attrs[$attribute->getAttributeCode()]  =   $attribute;
                }
        
                foreach ($productIds as $itemId => $productId) 
                {
                    $product        =   Mage::getModel('catalog/product')->load($productId);
                    $productBefore  =   $product;
                    $stockData      =   null;

                    // event was not dispached by some reasons
                    // if ($this->massactionEventDispatchEnabled)
                    //    Mage::dispatchEvent('catalog_product_prepare_save', array('product' => $product, 'request' => $this->getRequest()));
                    
                    $columnForUpdate        =   Iksanika_Productupdater_Block_Catalog_Product_Grid::getColumnForUpdate();
                    $columnForUpdateFlip    =   array_flip($columnForUpdate);

                    foreach($columnForUpdate as $columnName)
                    {
                        $columnValuesForUpdate = $this->getRequest()->getParam($columnName);

                        // handle exceptional situation or related tables savings
                        if($columnName == 'is_in_stock')
                        {
                            if(!$stockData)
                                $stockData = $product->getStockItem();
                            $stockData->setData('is_in_stock', $columnValuesForUpdate[$itemId]); 
                        }else
                        if($columnName == 'qty')
                        {
                            if(!$stockData)
                                $stockData = $product->getStockItem();

                            // check if relative value if received if not - save as it is
                            $columnValuesForUpdate[$itemId] = str_replace(' ', '', $columnValuesForUpdate[$itemId]);
                            if ($columnValuesForUpdate[$itemId][0] == '-')
                                $stockData->setData('qty', (float) ($stockData->getData('qty')) - (float) (substr($columnValuesForUpdate[$itemId], 1, strlen($columnValuesForUpdate[$itemId]))));
                            elseif ($columnValuesForUpdate[$itemId][0] == '+')
                                $stockData->setData('qty', (float) ($stockData->getData('qty')) + (float) (substr($columnValuesForUpdate[$itemId], 1, strlen($columnValuesForUpdate[$itemId]))));
                            elseif ($columnValuesForUpdate[$itemId][0] == '*')
                                $stockData->setData('qty', (float) ($stockData->getData('qty')) * (float) (substr($columnValuesForUpdate[$itemId], 1, strlen($columnValuesForUpdate[$itemId]))));
                            else
                                $stockData->setData('qty', $columnValuesForUpdate[$itemId]);
                            
                            //$stockData->setData('qty', $columnValuesForUpdate[$itemId]); // before v1.1.0
                        }else
                        if($columnName == 'category_ids')
                        {
                            $categoryIds = explode(',', $columnValuesForUpdate[$itemId]);
                            $product->setCategoryIds($categoryIds);
                        }else
                        if($columnName == 'related_ids')
                        {
                            
                            $relatedIds = trim($columnValuesForUpdate[$itemId]) != "" ? explode(',', str_replace(' ','',trim($columnValuesForUpdate[$itemId]))) : array();
                            $link = $this->getRelatedLinks($relatedIds, array(), $productId);
                            $product->setRelatedLinkData($link);
                        }else
                        if($columnName == 'cross_sell_ids')
                        {
                            $crossSellIds = trim($columnValuesForUpdate[$itemId]) != "" ? explode(',', str_replace(' ','',trim($columnValuesForUpdate[$itemId]))) : array();
                            $link = $this->getRelatedLinks($crossSellIds, array(), $productId);
                            $product->setCrossSellLinkData($link);
                        }else
                        if($columnName == 'up_sell_ids')
                        {
                            $upSellIds = trim($columnValuesForUpdate[$itemId]) != "" ? explode(',', str_replace(' ','',trim($columnValuesForUpdate[$itemId]))) : array();
                            $link = $this->getRelatedLinks($upSellIds, array(), $productId);
                            $product->setUpSellLinkData($link);
                        }else
                        if($columnName == 'attribute_set_id')
                        {
                        }else
                        if($columnName == 'associated_configure_ids')
                        {
                            //@TODO: Complex solutions should be done to implement this feature in product grid - waiting for next releases
                            $relatedIds = trim($columnValuesForUpdate[$itemId]) != "" ? explode(',', str_replace(' ','',trim($columnValuesForUpdate[$itemId]))) : array();
                        }else
                        if($columnName == 'associated_groupped_ids')
                        {
                            $relatedIds = trim($columnValuesForUpdate[$itemId]) != "" ? explode(',', str_replace(' ','',trim($columnValuesForUpdate[$itemId]))) : array();
                            $link = $this->getRelatedLinks($relatedIds, array(), $productId);
                            $product->setGroupedLinkData($link);
                        }else
                        if($columnName == 'tier_price' || $columnName == 'group_price')
                        {
                            $originalPrices = $product->getData($columnName);
                            
                            foreach($columnValuesForUpdate[$productId] as $index => $groupPrice)
                            {
                                if(is_array($groupPrice) && $groupPrice['price'] && is_array($originalPrices) && $originalPrices[$index])
                                {
                                    $columnValuesForUpdate[$productId][$index]['price'] = Mage::helper('productupdater')->recalculatePrice($originalPrices[$index]['price'], $groupPrice['price']);
                                }
                            }
                            $product->setData($columnName, $columnValuesForUpdate[$productId]);
                        }else
                        if($columnName == 'type_id')
                        {
                        }else
                        // check attributes types
                        if(isset($attrs[$columnName]) && ($attrs[$columnName]->getFrontendInput() == 'price'))
                        {
                            $columnValuesForUpdate = $this->getRequest()->getParam($columnName);
                            if(trim($columnValuesForUpdate[$itemId])!="")
                            {
                                $product->$columnName = Mage::helper('productupdater')->recalculatePrice($product->$columnName, $columnValuesForUpdate[$itemId]);

                            }
                        }else
                        if(isset($attrs[$columnName]) && ($attrs[$columnName]->getFrontendInput() == 'select'))
                        {
                            if($attrs[$columnName]->getIsRequired() && ($columnValuesForUpdate[$itemId] == ''))
                            {
                            }else
                            {
                                $product->$columnName =  $columnValuesForUpdate[$itemId];
                            }
                        }else
                        if(isset($attrs[$columnName]) && ($attrs[$columnName]->getFrontendInput() == 'media_image'))
                        {
                        }else
                        if(isset($attrs[$columnName]) && ($attrs[$columnName]->getFrontendInput() == 'image'))
                        {
                        }else
                            $product->$columnName =  $columnValuesForUpdate[$itemId];
                    }
                    
                    if($stockData)
                    {
                        $stockData->setStoreId(Mage::helper('productupdater')->getStoreId())->save();
                    }
                    $product->setStoreId(Mage::helper('productupdater')->getStoreId())->save();
                    
                    $attributeSetValues = $this->getRequest()->getParam('attribute_set_id', null);
                    
                    if($attributeSetValues && isset($attributeSetValues[$itemId]))
                    {
                        if($productBefore->getAttributeSetId() != $attributeSetValues[$itemId])
                        {
                            $product = Mage::getSingleton('catalog/product')
                                ->unsetData()
                                ->setStoreId(Mage::helper('productupdater')->getStoreId())
                                ->load($productId)
                                ->setAttributeSetId($attributeSetValues[$itemId])
                                ->save();
                        }
                    }
                }
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully refreshed.', count($productIds)));
            } catch (Exception $e) 
            {
                $this->_getSession()->addError($e->getMessage());
            }
        }else
        {
            $this->_getSession()->addError($this->__('Please select product(s)').'. '.$this->__('You should select checkboxes for each product row which should be updated. You can click on checkboxes or use CTRL+Click on product row which should be selected.'));
        }

        $this->_redirect('*/*/index', array('_current' => true));
    }
    
    
    public function exportCsvAction()
    {
        $content    = $this->getLayout()->createBlock('productupdater/catalog_product_grid')->getCsv();
        $this->_sendUploadResponse(self::$exportFileName.'.csv', $content);
    }

    public function exportXmlAction()
    {
        $content = $this->getLayout()->createBlock('productupdater/catalog_product_grid')->getXml();
        $this->_sendUploadResponse(self::$exportFileName.'.xml', $content);
    }
    
    
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

    
    
    public function getRelatedLinks($productIds, $existProducts, $productId)
    {
        $link = array();
        foreach ($productIds as $relatedToId) 
        {
            if ($productId != $relatedToId) 
            {
                $link[$relatedToId] = array('position' => null);
            }
        }
        // Fetch and append to already related products.
        foreach($existProducts as $existProduct)
        {
            $link[$existProduct->getId()] = array('position' => null);
        }
        return $link;
    }
    
     /**************************************************************************
     ** MAKE PRODUCTS RELATED
     **************************************************************************/
    
    
    /** 
     * Action make cheched products list related to each other.
     **/     
    public function massRelatedEachOtherAction()
    {
        $productIds = $this->getRequest()->getParam('product');
        if (is_array($productIds)) 
        {
            try {
                foreach ($productIds as $productId) 
                {
                    $product = Mage::getModel('catalog/product')->load($productId);
                    $link = $this->getRelatedLinks($productIds, $product->getRelatedProducts(), $productId);
                    $product->setRelatedLinkData($link);
                    
                    if ($this->massactionEventDispatchEnabled)
                    {
                        Mage::dispatchEvent('catalog_product_prepare_save', array('product' => $product, 'request' => $this->getRequest()));
                    }
                    $product->setStoreId(Mage::helper('productupdater')->getStoreId())->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully related to each other.', count($productIds)));
            } catch (Exception $e) 
            {
                $this->_getSession()->addError($e->getMessage());
            }
        }else
        {
            $this->_getSession()->addError($this->__('Please select product(s)'));
        }
        $this->_redirect('*/*/index', array('_current' => true));
    }
    
    
    
    /** 
     * Action which make selected products to specified products list (IDs)
     **/     
    public function massRelatedToAction()
    {
        $productIds = $this->getRequest()->getParam('product');
        $productIds2List = $this->getRequest()->getParam('callbackval');
        $productIds2 = explode(',', $productIds2List);
        
        if (is_array($productIds)) 
        {
            try {
                foreach ($productIds as $productId) 
                {
                    $product = Mage::getModel('catalog/product')->load($productId);
                    $link = $this->getRelatedLinks($productIds2, $product->getRelatedProducts(), $productId);
                    $product->setRelatedLinkData($link);

                    if ($this->massactionEventDispatchEnabled)
                    {
                        Mage::dispatchEvent('catalog_product_prepare_save', array('product' => $product, 'request' => $this->getRequest()));
                    }
                    $product->setStoreId(Mage::helper('productupdater')->getStoreId())->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully related to products('.$productIds2List.').', count($productIds)));
            } catch (Exception $e) 
            {
                $this->_getSession()->addError($e->getMessage());
            }
        }else
        {
            $this->_getSession()->addError($this->__('Please select product(s)'));
        }
        $this->_redirect('*/*/index', array('_current' => true));
    }
    
    
    
    /**
     * Action remove all relation in checked products list.
     **/     
    public function massRelatedCleanAction() 
    {
        $productIds = $this->getRequest()->getParam('product');
        if (is_array($productIds)) 
        {
            try {
                foreach ($productIds as $productId) 
                {
                    $product = Mage::getModel('catalog/product')->load($productId);
                    $product->setRelatedLinkData(array());
                    if ($this->massactionEventDispatchEnabled)
                    {
                        Mage::dispatchEvent('catalog_product_prepare_save', array('product' => $product, 'request' => $this->getRequest()));
                    }
                    $product->setStoreId(Mage::helper('productupdater')->getStoreId())->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) are no longer related to any other products.', count($productIds)));
            } catch (Exception $e) 
            {
                $this->_getSession()->addError($e->getMessage());
            }
        }else
        {
            $this->_getSession()->addError($this->__('Please select product(s)'));
        }
        $this->_redirect('*/*/index', array('_current' => true));
    }

    
    
    
    
    
     
    /***************************************************************************
     ** Cross-Selling
     **************************************************************************/  
    
    
    /** 
     * This will cross sell all products with each other.     
     **/  
    public function massCrossSellEachOtherAction()
    {
        $productIds = $this->getRequest()->getParam('product');
        if (is_array($productIds)) 
        {
            try {
                foreach ($productIds as $productId) 
                {
                    $product = Mage::getModel('catalog/product')->load($productId);
                    $link = $this->getRelatedLinks($productIds, $product->getCrossSellProducts(), $productId);
                    $product->setCrossSellLinkData($link);

                    if ($this->massactionEventDispatchEnabled)
                    {
                        Mage::dispatchEvent('catalog_product_prepare_save', array('product' => $product, 'request' => $this->getRequest()));
                    }
                    $product->setStoreId(Mage::helper('productupdater')->getStoreId())->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully cross-related to each other.', count($productIds)));
            } catch (Exception $e) 
            {
                $this->_getSession()->addError($e->getMessage());
            }
        }else
        {
            $this->_getSession()->addError($this->__('Please select product(s)'));
        }
        $this->_redirect('*/*/index', array('_current' => true));
    }
    
    
    
    /** 
     * This will relate all products to a specifc list of products 
     **/     
    public function massCrossSellToAction()
    {
        $productIds = $this->getRequest()->getParam('product');
        $productIds2List = $this->getRequest()->getParam('callbackval');
        $productIds2 = explode(',', $productIds2List);
        
        if (is_array($productIds)) 
        {
            try {
                foreach ($productIds as $productId) 
                {
                    $product = Mage::getModel('catalog/product')->load($productId);
                    $link = $this->getRelatedLinks($productIds2, $product->getCrossSellProducts(), $productId);
                    $product->setCrossSellLinkData($link);
                    
                    if ($this->massactionEventDispatchEnabled)
                    {
                        Mage::dispatchEvent('catalog_product_prepare_save', array('product' => $product, 'request' => $this->getRequest()));
                    }
                    $product->setStoreId(Mage::helper('productupdater')->getStoreId())->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully set as cross-sells by products('.$productIds2List.').', count($productIds)));
            } catch (Exception $e) 
            {
                $this->_getSession()->addError($e->getMessage());
            }
        }else
        {
            $this->_getSession()->addError($this->__('Please select product(s)'));
        }
        $this->_redirect('*/*/index', array('_current' => true));
    }
    
    
    /**
     * This will unrelate related product's relations.
     **/     
    public function massCrossSellClearAction() 
    {
        $productIds = $this->getRequest()->getParam('product');
        if (is_array($productIds)) 
        {
            try {
                foreach ($productIds as $productId) 
                {
                    $product = Mage::getModel('catalog/product')->load($productId);
                    $product->setCrossSellLinkData(array());
                    if ($this->massactionEventDispatchEnabled)
                    {
                        Mage::dispatchEvent('catalog_product_prepare_save', array('product' => $product, 'request' => $this->getRequest()));
                    }
                    $product->setStoreId(Mage::helper('productupdater')->getStoreId())->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) now have no products as cross sell links.', count($productIds)));
            } catch (Exception $e) 
            {
                $this->_getSession()->addError($e->getMessage());
            }
        }else
        {
            $this->_getSession()->addError($this->__('Please select product(s)'));
        }
        $this->_redirect('*/*/index', array('_current' => true));
    }
    
    
    
    
    
    /***************************************************************************
     ** Up-Selling
     **************************************************************************/  
    
    
    /** 
     * This will relate all products to a specifc list of products 
     **/
    public function massUpSellToAction()
    {
        $productIds = $this->getRequest()->getParam('product');
        $productIds2List = $this->getRequest()->getParam('callbackval');
        $productIds2 = explode(',', $productIds2List);
        
        if (is_array($productIds)) 
        {
            try {
                foreach ($productIds as $productId) 
                {
                    $product = Mage::getModel('catalog/product')->load($productId);
                    $link = $this->getRelatedLinks($productIds2, $product->getUpSellProducts(), $productId);
                    $product->setUpSellLinkData($link);
                    
                    if ($this->massactionEventDispatchEnabled)
                    {
                        Mage::dispatchEvent('catalog_product_prepare_save', array('product' => $product, 'request' => $this->getRequest()));
                    }
                    $product->setStoreId(Mage::helper('productupdater')->getStoreId())->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) are now up-sold by products('.$productIds2List.').', count($productIds)));
            } catch (Exception $e) 
            {
                $this->_getSession()->addError($e->getMessage());
            }
        }else
        {
            $this->_getSession()->addError($this->__('Please select product(s)'));
        }
        $this->_redirect('*/*/index', array('_current' => true));
    }
    
    
    
    /**
     * This will unrelate related product's relations.
     **/
    public function massUpSellClearAction() 
    {
        $productIds = $this->getRequest()->getParam('product');
        if (is_array($productIds)) 
        {
            try {
                foreach ($productIds as $productId) 
                {
                    $product = Mage::getModel('catalog/product')->load($productId);
                    $product->setUpSellLinkData(array());
                    if ($this->massactionEventDispatchEnabled)
                    {
                        Mage::dispatchEvent('catalog_product_prepare_save', array('product' => $product, 'request' => $this->getRequest()));
                    }
                    $product->setStoreId(Mage::helper('productupdater')->getStoreId())->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) now have 0 up-sells', count($productIds)));
            } catch (Exception $e) 
            {
                $this->_getSession()->addError($e->getMessage());
            }
        }else
        {
            $this->_getSession()->addError($this->__('Please select product(s)'));
        }
        $this->_redirect('*/*/index', array('_current' => true));
    }

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
     /**************************************************************************
     ** CATEGORIES ACTIONS STACK: ADD, REMOVE, REPLACE
     **************************************************************************/
    
    
    
    public function addCategoryAction()
    {
        $productIds =   $this->getRequest()->getParam('product');
        $storeId    =   (int)$this->getRequest()->getParam('store', 0);

        if (is_array($productIds)) 
        {
            try {
                
                $columnValuesForUpdate  =   $this->getRequest()->getParam('category');
                $categoryIdsAdd         =   explode(',', $columnValuesForUpdate);
                
                foreach ($productIds as $itemId => $productId) 
                {
                    $product            =   Mage::getModel('catalog/product')->load($productId);
                    $categoryIdsExist   =   $product->getCategoryIds($categoryIds);
                    $product->setCategoryIds(array_merge($categoryIdsAdd, $categoryIdsExist));
                    
                    $product->setStoreId(Mage::helper('productupdater')->getStoreId())->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully refreshed.', count($productIds)));
            } catch (Exception $e) 
            {
                $this->_getSession()->addError($e->getMessage());
            }
        }else
        {
            $this->_getSession()->addError($this->__('Please select product(s)').'. '.$this->__('You should select checkboxes for each product row which should be updated. You can click on checkboxes or use CTRL+Click on product row which should be selected.'));
        }
        $this->_redirect('*/*/index', array('_current' => true));
    }
    
    
    public function removeCategoryAction()
    {
        $productIds =   $this->getRequest()->getParam('product');
        $storeId    =   (int)$this->getRequest()->getParam('store', 0);

        if (is_array($productIds)) 
        {
            try {
                
                $columnValuesForUpdate = $this->getRequest()->getParam('category');
                $categoryIdsRemove  =   explode(',', $columnValuesForUpdate);
                $categoryIdsRemove  =   array_flip($categoryIdsRemove);
                
                foreach ($productIds as $itemId => $productId) 
                {
                    $product            =   Mage::getModel('catalog/product')->load($productId);
                    $categoryIdsExist   =   $product->getCategoryIds($categoryIds);
                    $categoryIdsFinal   =   array();

                    foreach($categoryIdsExist as $categoryId)
                    {
                        if(!isset($categoryIdsRemove[$categoryId]))
                            $categoryIdsFinal[] = $categoryId;
                    }
                    
                    $product->setCategoryIds($categoryIdsFinal);
                    
                    $product->setStoreId(Mage::helper('productupdater')->getStoreId())->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully refreshed.', count($productIds)));
            } catch (Exception $e) 
            {
                $this->_getSession()->addError($e->getMessage());
            }
        }else
        {
            $this->_getSession()->addError($this->__('Please select product(s)').'. '.$this->__('You should select checkboxes for each product row which should be updated. You can click on checkboxes or use CTRL+Click on product row which should be selected.'));
        }
        $this->_redirect('*/*/index', array('_current' => true));
    }
    
    
    public function replaceCategoryAction()
    {
        $productIds =   $this->getRequest()->getParam('product');
        $storeId    =   (int)$this->getRequest()->getParam('store', 0);

        if (is_array($productIds)) 
        {
            try {
                
                $columnValuesForUpdate  =   $this->getRequest()->getParam('category');
                $categoryIdsNew         =   explode(',', $columnValuesForUpdate);
                foreach ($productIds as $itemId => $productId) 
                {
                    $product            =   Mage::getModel('catalog/product')->load($productId);
                    $product->setCategoryIds($categoryIdsNew);
                    
                    $product->setStoreId(Mage::helper('productupdater')->getStoreId())->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully refreshed.', count($productIds)));
            } catch (Exception $e) 
            {
                $this->_getSession()->addError($e->getMessage());
            }
        }else
        {
            $this->_getSession()->addError($this->__('Please select product(s)').'. '.$this->__('You should select checkboxes for each product row which should be updated. You can click on checkboxes or use CTRL+Click on product row which should be selected.'));
        }
        $this->_redirect('*/*/index', array('_current' => true));
    }
    
    
    
    
    
    
    
    
    
    
    
    
    public function changeAttributeSetProductsAction()
    {
        $productIds =   $this->getRequest()->getParam('product');

        if (is_array($productIds)) 
        {
            try {
                $columnValuesForUpdate  =   $this->getRequest()->getParam('attribute_set');
                foreach ($productIds as $productId)
                {
                    Mage::getSingleton('catalog/product')
                        ->unsetData()->setStoreId(Mage::helper('productupdater')->getStoreId())->load($productId)
                        ->setAttributeSetId($columnValuesForUpdate)->setIsMassupdate(true)
                        ->save();
                    
                }
                Mage::dispatchEvent('catalog_product_massupdate_after', array('products' => $productIds));
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully refreshed.', count($productIds)));
            } catch (Exception $e) 
            {
                $this->_getSession()->addError($e->getMessage());
            }
        }else
        {
            $this->_getSession()->addError($this->__('Please select product(s)').'. '.$this->__('You should select checkboxes for each product row which should be updated. You can click on checkboxes or use CTRL+Click on product row which should be selected.'));
        }
        $this->_redirect('*/*/index', array('_current' => true));
    }
    
    
    
    
    
    public function updatePriceAction()
    {
        $productIds =   $this->getRequest()->getParam('product');

        if (is_array($productIds)) 
        {
            try {
                $columnValuesForUpdate  =   $this->getRequest()->getParam('price');
                foreach ($productIds as $productId)
                {
                    $product = Mage::getModel('catalog/product')->setStoreId(Mage::helper('productupdater')->getStoreId())->load($productId);
                    $product->setData('price', Mage::helper('productupdater')->recalculatePrice($product->getData('price'), $columnValuesForUpdate))->save();
                }
                Mage::dispatchEvent('catalog_product_massupdate_after', array('products' => $productIds));
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully refreshed.', count($productIds)));
            } catch (Exception $e) 
            {
                $this->_getSession()->addError($e->getMessage());
            }
        }else
        {
            $this->_getSession()->addError($this->__('Please select product(s)').'. '.$this->__('You should select checkboxes for each product row which should be updated. You can click on checkboxes or use CTRL+Click on product row which should be selected.'));
        }
        $this->_redirect('*/*/index', array('_current' => true));
    }
    
    public function updateCostAction()
    {
        $productIds =   $this->getRequest()->getParam('product');

        if (is_array($productIds)) 
        {
            try {
                $columnValuesForUpdate  =   $this->getRequest()->getParam('price');
                foreach ($productIds as $productId)
                {
                    $product = Mage::getModel('catalog/product')->setStoreId(Mage::helper('productupdater')->getStoreId())->load($productId);
                    $product->setData('cost', trim($columnValuesForUpdate) != "" ? Mage::helper('productupdater')->recalculatePrice($product->getData('cost'), $columnValuesForUpdate): '');
                    $product->save();
                }
                Mage::dispatchEvent('catalog_product_massupdate_after', array('products' => $productIds));
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully refreshed.', count($productIds)));
            } catch (Exception $e) 
            {
                $this->_getSession()->addError($e->getMessage());
            }
        }else
        {
            $this->_getSession()->addError($this->__('Please select product(s)').'. '.$this->__('You should select checkboxes for each product row which should be updated. You can click on checkboxes or use CTRL+Click on product row which should be selected.'));
        }
        $this->_redirect('*/*/index', array('_current' => true));
    }
    
    public function updateSpecialPriceAction()
    {
        $productIds =   $this->getRequest()->getParam('product');

        if (is_array($productIds)) 
        {
            try {
                $columnValuesForUpdate  =   $this->getRequest()->getParam('price');
                foreach ($productIds as $productId)
                {
                    $product = Mage::getModel('catalog/product')->setStoreId(Mage::helper('productupdater')->getStoreId())->load($productId);
                    $product->setData('special_price', trim($columnValuesForUpdate) != "" ?  Mage::helper('productupdater')->recalculatePrice($product->getData('special_price'), $columnValuesForUpdate) : '')->save();
                }
                Mage::dispatchEvent('catalog_product_massupdate_after', array('products' => $productIds));
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully refreshed.', count($productIds)));
            } catch (Exception $e) 
            {
                $this->_getSession()->addError($e->getMessage());
            }
        }else
        {
            $this->_getSession()->addError($this->__('Please select product(s)').'. '.$this->__('You should select checkboxes for each product row which should be updated. You can click on checkboxes or use CTRL+Click on product row which should be selected.'));
        }
        $this->_redirect('*/*/index', array('_current' => true));
    }
    
    public function updatePriceByCostAction()
    {
        $productIds =   $this->getRequest()->getParam('product');

        if (is_array($productIds)) 
        {
            try {
                $columnValuesForUpdate  =   $this->getRequest()->getParam('price');
                foreach ($productIds as $productId)
                {
                    $product = Mage::getModel('catalog/product')->setStoreId(Mage::helper('productupdater')->getStoreId())->load($productId);
                    $product->setData('price', Mage::helper('productupdater')->recalculatePrice($product->getData('cost'), $columnValuesForUpdate))->save();
                }
                Mage::dispatchEvent('catalog_product_massupdate_after', array('products' => $productIds));
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully refreshed.', count($productIds)));
            } catch (Exception $e) 
            {
                $this->_getSession()->addError($e->getMessage());
            }
        }else
        {
            $this->_getSession()->addError($this->__('Please select product(s)').'. '.$this->__('You should select checkboxes for each product row which should be updated. You can click on checkboxes or use CTRL+Click on product row which should be selected.'));
        }
        $this->_redirect('*/*/index', array('_current' => true));
    }
    
    public function updateSpecialPriceByCostAction()
    {
        $productIds =   $this->getRequest()->getParam('product');

        if (is_array($productIds)) 
        {
            try {
                $columnValuesForUpdate  =   $this->getRequest()->getParam('price');
                foreach ($productIds as $productId)
                {
                    $product = Mage::getModel('catalog/product')->setStoreId(Mage::helper('productupdater')->getStoreId())->load($productId);
                    $product->setData('special_price', Mage::helper('productupdater')->recalculatePrice($product->getData('cost'), $columnValuesForUpdate));
                    $product->save();
                }
                Mage::dispatchEvent('catalog_product_massupdate_after', array('products' => $productIds));
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully refreshed.', count($productIds)));
            } catch (Exception $e) 
            {
                $this->_getSession()->addError($e->getMessage());
            }
        }else
        {
            $this->_getSession()->addError($this->__('Please select product(s)').'. '.$this->__('You should select checkboxes for each product row which should be updated. You can click on checkboxes or use CTRL+Click on product row which should be selected.'));
        }
        $this->_redirect('*/*/index', array('_current' => true));
    }
    
    public function updateSpecialPriceByPriceAction()
    {
        $productIds =   $this->getRequest()->getParam('product');

        if (is_array($productIds)) 
        {
            try {
                $columnValuesForUpdate  =   $this->getRequest()->getParam('price');
                foreach ($productIds as $productId)
                {
                    $product = Mage::getModel('catalog/product')->setStoreId(Mage::helper('productupdater')->getStoreId())->load($productId);
                    $product->setData('special_price', Mage::helper('productupdater')->recalculatePrice($product->getData('price'), $columnValuesForUpdate))->save();
                }
                Mage::dispatchEvent('catalog_product_massupdate_after', array('products' => $productIds));
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully refreshed.', count($productIds)));
            } catch (Exception $e) 
            {
                $this->_getSession()->addError($e->getMessage());
            }
        }else
        {
            $this->_getSession()->addError($this->__('Please select product(s)').'. '.$this->__('You should select checkboxes for each product row which should be updated. You can click on checkboxes or use CTRL+Click on product row which should be selected.'));
        }
        $this->_redirect('*/*/index', array('_current' => true));
    }
    

    
    
    /*
     * @TODO: not finished action/function
     */
    
    
    public function copyAttributesAction()
    {
        $productIds =   $this->getRequest()->getParam('product');

        if (is_array($productIds)) 
        {
            try {
                $columnValuesForUpdate  =   $this->getRequest()->getParam('product');
                foreach ($productIds as $productId)
                {
//                    $product = Mage::getModel('catalog/product')->setStoreId(Mage::helper('productupdater')->getStoreId())->load($productId);
//                    $product->setData('special_price', Mage::helper('productupdater')->recalculatePrice($product->getData('price'), $columnValuesForUpdate))->save();
                }
                Mage::dispatchEvent('catalog_product_massupdate_after', array('products' => $productIds));
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully refreshed.', count($productIds)));
            } catch (Exception $e) 
            {
                $this->_getSession()->addError($e->getMessage());
            }
        }else
        {
            $this->_getSession()->addError($this->__('Please select product(s)').'. '.$this->__('You should select checkboxes for each product row which should be updated. You can click on checkboxes or use CTRL+Click on product row which should be selected.'));
        }
        $this->_redirect('*/*/index', array('_current' => true));
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    public function duplicateProductsAction()
    {
        $productIds =   $this->getRequest()->getParam('product');

        if (is_array($productIds)) 
        {
            try {
                
                foreach ($productIds as $itemId => $productId) 
                {
                    $product    =   Mage::getModel('catalog/product')->load($productId);
                    $clone      =   $product->duplicate();
                    $clone->setSku($product->getSku().'-#1');
                    $clone->setVisibility($clone->getVisibility());   
                    $clone->setStatus($clone->getStatus());
                    $clone->setTaxClassId($clone->getTaxClassId());
                    $clone->setCategoryIds($clone->getCategoryIds());
                    $clone->getResource()->save($clone);
                    //$product->setStoreId(Mage::helper('productupdater')->getStoreId())->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully refreshed.', count($productIds)));
            } catch (Exception $e) 
            {
                $this->_getSession()->addError($e->getMessage());
            }
        }else
        {
            $this->_getSession()->addError($this->__('Please select product(s)').'. '.$this->__('You should select checkboxes for each product row which should be updated. You can click on checkboxes or use CTRL+Click on product row which should be selected.'));
        }
        $this->_redirect('*/*/index', array('_current' => true));
    }
    
    
    public function saveColumnPositionsAction()
    {
        $orderedFields = Mage::app()->getRequest()->getParam('fields', array());
        $config = Mage::getModel('core/config');
        $config->saveConfig('productupdater/attributes/positions' . Mage::getSingleton('admin/session')->getUser()->getId(), implode(',', $orderedFields));
        $config->cleanCache();
        $result = array('success' => 1);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
    
    
    
    
    
    
    
    public function saveSettingsSectionAction()
    {
        $settingsFields = Mage::app()->getRequest()->getParam('settings', array());
        $config = Mage::getModel('core/config');
        $config->saveConfig('productupdater/images/width', $settingsFields['images']['width']);
        $config->saveConfig('productupdater/images/height', $settingsFields['images']['height']);
        $config->saveConfig('productupdater/images/scale', $settingsFields['images']['scale']);
        $config->saveConfig('productupdater/images/showurl', $settingsFields['images']['showurl']);
        $config->saveConfig('productupdater/columns/associatedShow', $settingsFields['columns']['associatedShow']);
        $config->saveConfig('productupdater/columns/redirectAdvancedProductManager', $settingsFields['columns']['redirectAdvancedProductManager']);
        $resetPositions = (Mage::getStoreConfig('productupdater/columns/showcolumns') != $settingsFields['columns']['showcolumns']) ? true : false;
        $config->saveConfig('productupdater/columns/showcolumns', $settingsFields['columns']['showcolumns']);
        if($resetPositions)
        {
            $config->saveConfig('productupdater/attributes/positions' . Mage::getSingleton('admin/session')->getUser()->getId(), '');
        }
        $config->cleanCache();
        
        $result = array('success' => 1);

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
    
    
    
    
    
    
    
    
    
    
}