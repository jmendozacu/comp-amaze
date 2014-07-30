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

class Apptha_Amazereviews_Block_Adminhtml_Amazereviews_Grid_Renderer_Viewreview extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    //FUNCTION TO VIEW THE CORESSPONDING REVIEW IN ALL REVIEWS PAGE
    public function render(Varien_Object $row)
    {
    	//TO GET THE CORRESPONDING REVIEW'S REVIEW ID
        $id=$row->getReviewId();
       //TO REDIRECT THE PAGE TO CORRESPONDING REVIEW EDIT PAGE IN ALL REVIEWS GRID
        return '<a href="'.$this->getUrl('adminhtml/catalog_product_review/edit', array('id'=>$id)).'">Edit Review</a>';
    }    
}