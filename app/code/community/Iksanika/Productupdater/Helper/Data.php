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

class Iksanika_Productupdater_Helper_Data extends Mage_Core_Helper_Abstract 
{
    
    protected static $showImageBase = null;
    protected static $showImageSmall = null;
    protected static $showImageThumbnail = null;
    
    public static function initSettings()
    {
        self::$showImageBase        =   (!self::$showImageBase) ? ((int)Mage::getStoreConfig('productimages/images/image_base') === 1) : null;
        self::$showImageSmall       =   (!self::$showImageSmall) ? ((int)Mage::getStoreConfig('productimages/images/image_small') === 1) : null;
        self::$showImageThumbnail   =   (!self::$showImageThumbnail) ? (int)Mage::getStoreConfig('productimages/images/image_thumbnail') === 1 : null;
    }
    
    public function showImageBase()
    {
        self::initSettings();
        return self::$showImageBase;
    }
    
    public function showImageSmall()
    {
        self::initSettings();
        return self::$showImageSmall;
    }
    
    public function showImageThumbnail()
    {
        self::initSettings();
        return self::$showImageThumbnail;
    }
    
    public function getImageUrl($image_file)
    {
        $url = false;
        $url = Mage::getBaseUrl('media').'catalog/product'.$image_file;
        return $url;
    }
    
    public function getFileExists($image_file)
    {
        $file_exists = false;
        $file_exists = file_exists('media/catalog/product'. $image_file);
        return $file_exists;
    }
    
    public function getStoreId()
    {
        return (int) Mage::app()->getRequest()->getParam('store', 0);
    }
    
    public function getStore()
    {
        return Mage::app()->getStore($this->getStoreId());
    }
    
    
    
    
    // get list of all products attributes
    public function getAttributesList()
    {
        $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
                         ->addVisibleFilter()
                         ->addStoreLabel(Mage::helper('productupdater')->getStoreId());
        $attributes->getSelect();
        return $attributes;
    }
    
    
    public function getGridAttributes()
    {
        $selected = (string) Mage::getStoreConfig('productupdater/attributes/positions' . Mage::getSingleton('admin/session')->getUser()->getId());
        return ($selected) ? explode(',', $selected) : array();
    }
    
    public function getSelectedAttributes()
    {
        return $this->getGridAttributes();
    }
    
    
    public function recalculatePrice($originalPrice, $newPrice)
    {
        if (!preg_match('/^[0-9]+(\.[0-9]+)?$/', $newPrice))
        {
            if (!preg_match('/^[+-][0-9]+(\.[0-9]+)?%?$/', $newPrice))
            {
                throw new Exception(Mage::helper('productupdater')->__('Please provide the difference as +5.25 -5.25 +5.25% or -5.25%')); 
            }else
            {
                $sign       =   substr($newPrice, 0, 1);
                $newPrice   =   substr($newPrice, 1);
                $percent    =   (substr($newPrice, -1, 1) == '%');
                if ($percent)
                    $newPrice = substr($newPrice, 0, -1);

                $newPrice = floatval($newPrice);
                if ($newPrice < 0.00001)
                {
                    throw new Exception(Mage::helper('productupdater')->__('Please provide a non empty difference'));            
                }


                $value = $percent ? ($originalPrice * $newPrice / 100) : $newPrice;

                if($sign == '+')
                {
                    $value = $originalPrice + $value;
                }else
                if($sign == '-')
                {
                    $value = $originalPrice - $value;
                }
                return $value;
            }
        }else
            return $newPrice;
    }
    
    
    
}
