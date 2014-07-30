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

class Iksanika_Productupdater_Block_Widget_Grid_Column 
    extends Mage_Adminhtml_Block_Widget_Grid_Column
{
    
    protected function _getRendererByType()
    {
        switch (strtolower($this->getType())) {
            case 'image':
                $rendererClass = 'productupdater/widget_grid_column_renderer_image';
            break;
        
            case 'input':
            case 'iks_sku':
                $rendererClass = 'productupdater/widget_grid_column_renderer_input'; // adminhtml/widget_grid_column_renderer_input
            break;
            
            case 'iks_percentage':
                $rendererClass = 'productupdater/widget_grid_column_renderer_percentage';
            break;

            default:
                $rendererClass = parent::_getRendererByType();
            break;
        }
        
        return $rendererClass;
    }

    protected function _getFilterByType()
    {
        switch (strtolower($this->getType())) {
            case 'select':
                $filterClass = 'adminhtml/widget_grid_column_filter_select';
            break;
        
            case 'image':
                $filterClass = 'productupdater/widget_grid_column_filter_image';
            break;
            /*
            case 'iks_options':
                $filterClass = 'adminhtml/widget_grid_column_filter_select';
            break;
             */
        
            case 'iks_sku':
                $filterClass = 'productupdater/widget_grid_column_filter_sku';
            break;
        
            case 'iks_percentage':
                $filterClass = 'adminhtml/widget_grid_column_filter_text';
            break;
        
            default:
                $filterClass = parent::_getFilterByType();
            break;
        }
        return $filterClass;
    }

}