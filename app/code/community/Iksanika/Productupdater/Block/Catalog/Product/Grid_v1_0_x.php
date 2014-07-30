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

class Iksanika_Productupdater_Block_Catalog_Product_Grid extends Iksanika_Productupdater_Block_Widget_Grid //Mage_Adminhtml_Block_Widget_Grid
{
    protected $isenhanced = true;
    private $isenabled = true;

    private static $columnSettings = array();
    
    protected static $columnType = array(
        'id'                        =>  array('type'=>'number'),
        'product'                   =>  array('type'=>'checkbox'),
        
        'thumbnail'                 =>  array('type'=>'image'),
        'small_image'               =>  array('type'=>'image'),
        'image'                     =>  array('type'=>'image'),
        'name'                      =>  array('type'=>'input'),
        'custom_name'               =>  array('index'=>'custom_name'),
        'type_id'                   =>  array('type'=>'options'),
        'attribute_set_id'          =>  array('type'=>'options'),
        'sku'                       =>  array('type'=>'input'),
//        'sku'                   =>  array('type'=>'iks_sku'),
        'category_ids'              =>  array('type'=>'input'),
        'price'                     =>  array('type'=>'price'),
        'qty'                       =>  array('type'=>'input'),
        'is_in_stock'               =>  array('type'=>'iks_options'),
        'visibility'                =>  array('type'=>'iks_options'),
        'status'                    =>  array('type'=>'iks_options'),
        'websites'                  =>  array('type'=>'options'),
        'cost'                      =>  array('type'=>'price'),
        'weight'                    =>  array('type'=>'number'),
        'url_key'                   =>  array('type'=>'input'),
        'tier_price'                =>  array('type'=>'price'),
        'tax_class_id'              =>  array('type'=>'iks_options'),
        'special_to_date'           =>  array('type'=>'date'),
        'special_price'             =>  array('type'=>'price'),
        'special_from_date'         =>  array('type'=>'date'),
        'color'                     =>  array('type'=>'text'),
        'size'                      =>  array('type'=>'text'),
        'brand'                     =>  array('type'=>'text'),
        'custom_design'             =>  array('type'=>'text'),
        'custom_design_from'        =>  array('type'=>'date'),
        'custom_design_to'          =>  array('type'=>'date'),
        'default_category_id'       =>  array('type'=>'text'),
        'dimension'                 =>  array('type'=>'text'),
        'manufacturer'              =>  array('type'=>'text'),
        'meta_keyword'              =>  array('type'=>'iks_textarea'),
        'meta_description'          =>  array('type'=>'iks_textarea'),
        'meta_title'                =>  array('type'=>'input'),
        'short_description'         =>  array('type'=>'iks_textarea'),
        'description'               =>  array('type'=>'iks_textarea'),
        'country_of_manufacture'    =>  array('type'=>'country'),
        'enable_googlecheckout'     =>  array('type'=>'iks_options'),
        'gift_message_available'    =>  array('type'=>'iks_options'),
        'gallery'                   =>  array('type'=>'text'),
        'media_gallery'             =>  array('type'=>'text'),
        'is_recurring'              =>  array('type'=>'iks_options'),
        'group_price'               =>  array('type'=>'text'),

        'msrp'                      =>  array('type'=>'price'),
        'msrp_display_actual_price_type'=>  array('type'=>'text'),
        'msrp_enabled'              =>  array('type'=>'text'),
        'news_from_date'            =>  array('type'=>'date'),
        'news_to_date'              =>  array('type'=>'date'),
        
        'options_container'         =>  array('type'=>'text'),
        'page_layout'               =>  array('type'=>'text'),
        'price_view'                =>  array('type'=>'text'),
        'recurring_profile'         =>  array('type'=>'text'),
        
        'related_ids'               =>  array('type'=>'input'),
        'cross_sell_ids'            =>  array('type'=>'input'),
        'up_sell_ids'               =>  array('type'=>'input'),
    );
    
    
    
    
    
    public function __construct()
    {
        parent::__construct();
        $this->isenabled = Mage::getStoreConfig('productupdater/general/isenabled');
        $this->isAllowed = array(
            'related' => (int)Mage::getStoreConfig('productupdater/productrelator/enablerelated') === 1,
            'cross_sell' => (int)Mage::getStoreConfig('productupdater/productrelator/enablecrosssell') === 1,
            'up_sell' => (int)Mage::getStoreConfig('productupdater/productrelator/enableupsell') === 1
        );
        
        $this->setId('productGrid');
        
        $this->prepareDefaults();
        
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
//        $this->setVarNameFilter('product_filter');
        
        self::prepareColumnSettings();
        $this->setTemplate('iksanika/productupdater/catalog/product/grid.phtml');
        $this->setMassactionBlockName('productupdater/widget_grid_massaction');
        
    }
    
    private function prepareDefaults() 
    {
        $this->setDefaultLimit(Mage::getStoreConfig('productupdater/columns/limit'));
        $this->setDefaultPage(Mage::getStoreConfig('productupdater/columns/page'));
        $this->setDefaultSort(Mage::getStoreConfig('productupdater/columns/sort'));
        $this->setDefaultDir(Mage::getStoreConfig('productupdater/columns/dir'));
    }
    
    public static function prepareColumnSettings() 
    {
        $storeSettings = Mage::getStoreConfig('productupdater/columns/showcolumns');
        $tempArr = explode(',', $storeSettings);
        
        foreach($tempArr as $showCol) 
        {
            self::$columnSettings[trim($showCol)] = true;
        }
    }
    
    public static function getColumnSettings()
    {
        if(count(self::$columnSettings) == 0)
        {
            self::prepareColumnSettings();
        }
        return self::$columnSettings;
    }
    
    public static function getColumnForUpdate()
    {
        $fields = array('product');
        
        if(count(self::getColumnSettings()))
        {
            foreach(self::getColumnSettings() as $columnId => $status)
            {
                if(isset(self::$columnType[$columnId]))
                {
                    if(
                        self::$columnType[$columnId]['type'] == 'input' || 
                        self::$columnType[$columnId]['type'] == 'price' || 
                        self::$columnType[$columnId]['type'] == 'number' || 
                        //self::$columnType[$columnId]['type'] == 'options' || 
                        self::$columnType[$columnId]['type'] == 'iks_options' || 
                        self::$columnType[$columnId]['type'] == 'iks_textarea' || 
                        self::$columnType[$columnId]['type'] == 'date' 
                      )
                    {
                        $fields[] = $columnId;
                    }
                }
                // enhacement - start
                // || !isset(self::$columnType[$columnId])
                /*
                else
                {
                    $fields[] = $columnId;
                }*/
                // enhacement - end


            }
        }
        return $fields;
    }
    
    public function colIsVisible($code) 
    {
        return isset(self::$columnSettings[$code]);
    }
    
    
    protected function _prepareLayout()
    {
        $this->setChild('export_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('adminhtml')->__('Export'),
                    'onclick'   => $this->getJsObjectName().'.doExport()',
                    'class'   => 'task'
                ))
        );
        return parent::_prepareLayout();
    }
    
    protected function _setFilterValues($data)
    {
        foreach ($this->getColumns() as $columnId => $column) 
        {
            if (
                isset($data[$columnId])
                && (!empty($data[$columnId]) || strlen($data[$columnId]) > 0)
                && $column->getFilter()) 
            {
                $column->getFilter()->setValue($data[$columnId]);
                if($columnId != 'category_ids')
                    $this->_addColumnFilterToCollection($column);
            }
        }
        return $this;
    }
    
    /**
     * Sets sorting order by some column
     *
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _setCollectionOrder($column)
    {
        $collection = $this->getCollection();
        if ($collection) {
            $columnIndex = $column->getFilterIndex() ?
                $column->getFilterIndex() : $column->getIndex();
            $columnIndex = ($columnIndex == 'category_ids') ? 'cat_ids' : $columnIndex;
            $collection->setOrder($columnIndex, strtoupper($column->getDir()));
        }
        return $this;
    }
    
    public function getQuery() 
    {
        return urldecode($this->getParam('q'));
    }
    
    protected function _prepareCollection()
    {
        $collection = $this->getCollection();
        $collection = !$collection ? Mage::getModel('catalog/product')->getCollection() : $collection;
        if($queryString = $this->getQuery()) 
        {
            $query = Mage::helper('catalogSearch')->getQuery();
            $query->setStoreId(Mage::app()->getStore()->getId());
            $query->setQueryText(Mage::helper('catalogsearch')->getQuery()->getQueryText());

            $collection = $query->getSearchCollection();
            $collection->addSearchFilter(Mage::helper('catalogsearch')->getQuery()->getQueryText());
            $collection->addAttributeToSelect('*');
            //$collection->addAttributeToFilter('status', 1);
        }
        
        $store = $this->_getStore();
        $collection 
            ->joinField(
                'qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left')
            ->joinField(
                'is_in_stock',
                'cataloginventory/stock_item',
                'is_in_stock',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left')
            ->joinField(
                'cat_ids',
                'catalog/category_product',
                'category_id',
                'product_id=entity_id',
                null,
                'left')
            ->joinField(
                'category',
                'catalog/category_product',
                'category_id',
                'product_id=entity_id',
                null,
                'left')
            ->joinField(
                'related_ids',
                'catalog/product_link',
                'linked_product_id',
                'product_id=entity_id',
                '{{table}}.link_type_id=1', // 1- relation, 4 - up_sell, 5 - cross_sell
                'left')
            ->joinField(
                'cross_sell_ids',
                'catalog/product_link',
                'linked_product_id',
                'product_id=entity_id',
                '{{table}}.link_type_id=5', // 1- relation, 4 - up_sell, 5 - cross_sell
                'left')
            ->joinField(
                'up_sell_ids',
                'catalog/product_link',
                'linked_product_id',
                'product_id=entity_id',
                '{{table}}.link_type_id=4', // 1- relation, 4 - up_sell, 5 - cross_sell
                'left')
            ;
        
        $collection->groupByAttribute('entity_id');

        if ($store->getId()) 
        {
            //$collection->setStoreId($store->getId());
            $collection->addStoreFilter($store);
            $collection->joinAttribute(
                'custom_name', 
                'catalog_product/name', 
                'entity_id', 
                null, 
                'inner', 
                $store->getId()
            );
            $collection->joinAttribute(
                'status', 
                'catalog_product/status', 
                'entity_id', 
                null, 
                'inner', 
                $store->getId()
            );
            $collection->joinAttribute(
                'visibility', 
                'catalog_product/visibility', 
                'entity_id', 
                null, 
                'inner', 
                $store->getId()
            );
            $collection->joinAttribute(
                'price', 
                'catalog_product/price', 
                'entity_id', 
                null, 
                'left', 
                $store->getId()
            );
        }
        else {
            $collection->addAttributeToSelect('price');
            $collection->addAttributeToSelect('status');
            $collection->addAttributeToSelect('visibility');
        }
        
        // EG: Select all needed columns.
        //id,name,type,attribute_set,sku,price,qty,visibility,status,websites,image
        foreach(self::$columnSettings as $col => $true) 
        {
            if($col == 'category_ids')
            {
                //$filter = $this->getParam('filter');
//                echo $this->getVarNameFilter().'~';
                $filter = $this->getParam($this->getVarNameFilter());
                if($filter)
                {
                    $filter_data = Mage::helper('adminhtml')->prepareFilterString($filter);
                    if(isset($filter_data['category_ids']))
                    {
                        if(trim($filter_data['category_ids'])=='')
                            continue;
                        $categoryIds = explode(',', $filter_data['category_ids']);
                        $catIdsArray = array();
                        foreach($categoryIds as $categoryId)
                        {
                            //$collection->addCategoryFilter(Mage::getModel('catalog/category')->load($categoryId));
                            $catIdsArray[] = $categoryId;
                        }
                        $collection->addAttributeToFilter('cat_ids', array( 'in' => $catIdsArray));                        
                        //$collection->printLogQuery(true);
                    }
                }
            }
            if($col == 'related_ids' || $col == 'cross_sell_ids' || $col == 'up_sell_ids')
            { 
                $filter = $this->getParam($this->getVarNameFilter());
                if($filter)
                {
                    $filter_data = Mage::helper('adminhtml')->prepareFilterString($filter);
                    if(isset($filter_data[$col]))
                    {
                        if(trim($filter_data[$col])=='')
                            continue;
                        $relatedIds = explode(',', $filter_data[$col]);
                        $relatedIdsArray = array();
                        foreach($relatedIds as $relatedId)
                        {
                            //$collection->addCategoryFilter(Mage::getModel('catalog/category')->load($categoryId));
                            $relatedIdsArray[] = intval($relatedId);
                        }
                        $collection->addAttributeToFilter($col, array( 'in' => $relatedIdsArray));                        
                    }
                }
            }
            /*
            if($col == 'sku')
            {
                $filter = $this->getParam($this->getVarNameFilter());
                if($filter)
                {
                    $filter_data = Mage::helper('adminhtml')->prepareFilterString($filter);
                    if(isset($filter_data['sku']))
                    {
                        if(trim($filter_data['sku'])=='')
                            continue;
                        $skuIds = explode(',', $filter_data['sku']);
                        $skuIdsArray = array();
                        foreach($skuIds as $skuId)
                            $skuIdsArray[] = $skuId;
                        $collection->addAttributeToFilter('sku', array( 'inset' => $skuIdsArray));                        
                    }
                }
            }
           */
            if($col == 'qty' || $col == 'websites' || $col=='id' || $col=='category_ids' || $col=='related_ids'|| $col=='cross_sell_ids'|| $col=='up_sell_ids' || $col=='group_price') 
                continue;
            else
                $collection->addAttributeToSelect($col);
        }

        $this->setCollection($collection);
        parent::_prepareCollection();
        //$collection->printLogQuery(true);
        $collection->addWebsiteNamesToResult();

        return $this;
    }


    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
/*
    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            if ($column->getId() == 'websites') {
                $this->getCollection()->joinField('websites',
                    'catalog/product_website',
                    'website_id',
                    'product_id=entity_id',
                    null,
                    'left');
            }
        }
        return parent::_addColumnFilterToCollection($column);
    }
 */

    
    
    public function _applyMyFilter($column)
    {
        // empty filter condition to avoid standard magento conditions
    }
            
    protected function _prepareColumns()
    {
        $store = $this->_getStore();
        
        if($this->colIsVisible('id')) 
        {
            $this->addColumn('id',
                array(
                    'header'=> Mage::helper('catalog')->__('ID'),
                    'width' => '50px',
                    'type'  => 'number',
                    'index' => 'entity_id',
            ));
        }
        
        $imgWidth = Mage::getStoreConfig('productupdater/images/width') + "px";
        
        if($this->colIsVisible('thumbnail')) 
        {
            $this->addColumn('thumbnail',
                array(
                    'header'=> Mage::helper('catalog')->__('Thumbnail'),
                    'type'  => 'image',
                    'width' => $imgWidth,
                    'index' => 'thumbnail',
                    'renderer' => 'Iksanika_Productupdater_Block_Widget_Grid_Column_Renderer_Image',
            ));
        }
        
        if($this->colIsVisible('small_image')) 
        {
            $this->addColumn('small_image',
                array(
                    'header'=> Mage::helper('catalog')->__('Small Img'),
                    'type'  => 'image',
                    'width' => $imgWidth,
                    'index' => 'small_image',
                    'renderer' => 'Iksanika_Productupdater_Block_Widget_Grid_Column_Renderer_Image',
            ));
        }
        
        if($this->colIsVisible('image')) 
        {
            $this->addColumn('image',
                array(
                    'header'=> Mage::helper('catalog')->__('Image'),
                    'type'  => 'image',
                    'width' => $imgWidth,
                    'index' => 'image',
                    'renderer' => 'Iksanika_Productupdater_Block_Widget_Grid_Column_Renderer_Image',
            ));
        }
        
        if($this->colIsVisible('name')) 
        {
            $this->addColumn('name',
                array(
                    'header'=> Mage::helper('catalog')->__('Name'),
                    'type' => 'input',
                    'name' => 'pu_name[]',
                    'index' => 'name'/*,
                    'width' => '150px'*/
            ));
        }
        if($this->colIsVisible('name')) 
        {
            if ($store->getId()) 
            {
                $this->addColumn('custom_name',
                    array(
                        'header'=> Mage::helper('catalog')->__('Name In %s', $store->getName()),
                        'index' => 'custom_name',
                        'width' => '150px'
                ));
            }
        }

        if($this->colIsVisible('type_id')) 
        {
            $this->addColumn('type',
                array(
                    'header'=> Mage::helper('catalog')->__('Type'),
                    'width' => '60px',
                    'index' => 'type_id',
                    'type'  => 'options',
                    'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
            ));
        }

        if($this->colIsVisible('attribute_set_id')) 
        {
            $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
                ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
                ->load()
                ->toOptionHash();
    
            $this->addColumn('set_name',
                array(
                    'header'=> Mage::helper('catalog')->__('Attrib. Set Name'),
                    'width' => '100px',
                    'index' => 'attribute_set_id',
                    'type'  => 'options',
                    'options' => $sets,
            ));
        }
        
        if($this->colIsVisible('sku')) 
        {
            $this->addColumn('sku',
                array(
                    'header'=> Mage::helper('catalog')->__('SKU'),
                    'width' => '80px',
                    'index' => 'sku',
                    'name' => 'pu_sku[]',
                    'filter' => 'Iksanika_Productupdater_Block_Widget_Grid_Column_Filter_Sku',
                    'type' => 'input'
            ));
        }
        
        if($this->colIsVisible('category_ids')) 
        {
            $this->addColumn('category_ids',
                array(
                    'header'=> Mage::helper('catalog')->__('Category ID\'s'),
                    'width' => '80px',
                    'index' => 'category_ids',
                    'name' => 'pu_category_ids[]',
                    'type' => 'input'
            ));
        }
        
        if($this->colIsVisible('category')) 
        {
            $this->addColumn('category',
                array(
                    'header' => Mage::helper('catalog')->__('Categories'),
                    'index' => 'category',
                    'renderer' => Iksanika_Productupdater_Block_Widget_Grid_Column_Renderer_Category,
                    'sortable' => false,
                    'filter' => Iksanika_Productupdater_Block_Widget_Grid_Column_Filter_Category,
                    'type' => 'options',
                    'options' => Mage::helper('productupdater/category')->getOptionsForFilter(),
            ));
        }


        if($this->colIsVisible('price')) 
        {
            $this->addColumn('price',
                array(
                    'header'=> Mage::helper('catalog')->__('Price'),
//                    'type'  => 'input',
                    'type'  => 'price',
                    'currency_code' => $store->getBaseCurrency()->getCode(),
                    'index' => 'price',
                    'name' => 'pu_price[]',
                    'renderer' => 'Iksanika_Productupdater_Block_Widget_Grid_Column_Renderer_Price',
                    //'currency_code' => $currency
            ));
        }

        if($this->colIsVisible('qty')) 
        {
            $this->addColumn('qty',
                array(
                    'header'=> Mage::helper('catalog')->__('Qty'),
                    'width' => '100px',
                    'type'  => 'input',
                    'index' => 'qty',
                    'name' => 'pu_qty[]',
                    'renderer' => 'Iksanika_Productupdater_Block_Widget_Grid_Column_Renderer_Number',
            ));
        }

        if($this->colIsVisible('is_in_stock')) 
        {
            $this->addColumn('is_in_stock',
                array(
                    'header'=> Mage::helper('catalog')->__('Is in Stock'),
                    'width' => '100px',
//                    'type' => 'iks_options', 
                    'type' => 'options', 
                    'index' => 'is_in_stock',
                    'name' => 'pu_is_in_stock[]',
                    'options' => array(0 => __('No'), 1 => __('Yes')),
                    'renderer' => 'Iksanika_Productupdater_Block_Widget_Grid_Column_Renderer_Options',
            ));
        }
        
        if($this->colIsVisible('visibility')) 
        {
            $this->addColumn('visibility',
                array(
                    'header'=> Mage::helper('catalog')->__('Visibility'),
                    'width' => '70px',
                    'index' => 'visibility',
//                    'type'  => 'iks_options',
                    'type'  => 'options',
                    'options' => Mage::getModel('catalog/product_visibility')->getOptionArray(),
                    'renderer' => 'Iksanika_Productupdater_Block_Widget_Grid_Column_Renderer_Options',
            ));
        }

        if($this->colIsVisible('status')) 
        {
            $this->addColumn('status',
                array(
                    'header'=> Mage::helper('catalog')->__('Status'),
                    'width' => '70px',
                    'index' => 'status',
//                    'type'  => 'iks_options',
                    'type'  => 'options',
                    'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
                    'renderer' => 'Iksanika_Productupdater_Block_Widget_Grid_Column_Renderer_Options',
            ));
        }

        if($this->colIsVisible('websites')) 
        {
            if (!Mage::app()->isSingleStoreMode()) {
                $this->addColumn('websites',
                    array(
                        'header'=> Mage::helper('catalog')->__('Websites'),
                        'width' => '100px',
                        'sortable'  => false,
                        'index'     => 'websites',
                        'type'      => 'options',
                        'options'   => Mage::getModel('core/website')->getCollection()->toOptionHash(),
                ));
            }
        }

        $ignoreCols = array(
            'id'=>true, 
            'websites'=>true,
            'status'=>true,
            'visibility'=>true,
            'qty'=>true,
            'is_in_stock'=>true,
            'price'=>true,
            'sku'=>true,
            'attribute_set_id'=>true, 
            'type_id'=>true,
            'name'=>true, 
            'image'=>true, 
            'thumbnail' => true, 
            'small_image'=>true, 
            'category_ids' => true,
            'category' => true,
        );
        
        $currency = $store->getBaseCurrency()->getCode();
        
        $taxClassCollection = Mage::getModel('tax/class_source_product')->toOptionArray();
        $taxClasses = array();
        foreach($taxClassCollection as $taxClassItem)
            $taxClasses[$taxClassItem['value']] = $taxClassItem['label'];
        
        $defaults = array(
            'country_of_manufacture'  => array(
                'type' => 'country', 
                'width'=>'30px', 
                'header'=> Mage::helper('catalog')->__('Country of Manufacture'), 
                'name' => 'pu_country[]'),
            'cost'  => array(
                'type'=>'price', 
                'width'=>'30px', 
                'header'=> Mage::helper('catalog')->__('Cost'), 
                'name' => 'pu_cost[]',
                'renderer' => 'Iksanika_Productupdater_Block_Widget_Grid_Column_Renderer_Price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
//                'currency_code' => $currency
                ),
            'weight'  => array(
                'header'=> Mage::helper('catalog')->__('Weight'),
                'width'=>'30px', 
                'type'=>'number',
                'index' => 'weight',
                'renderer' => 'Iksanika_Productupdater_Block_Widget_Grid_Column_Renderer_Number'),
            'tier_price'  => array(
                'type'=>'price', 
                'width'=>'100px', 
                'header'=> Mage::helper('catalog')->__('Tier Price'), 
                'name' => 'pu_tier_price[]',
                'type' => 'price',
                'currency_code' => $currency),
            'tax_class_id'  => array(
                'width'=>'100px', 
                'header'=> Mage::helper('catalog')->__('Tax Class ID'),
                'index' => 'tax_class_id',
//                'type'  => 'iks_options',
                'type'  => 'options',
                'options' => $taxClasses,
                'renderer' => 'Iksanika_Productupdater_Block_Widget_Grid_Column_Renderer_Options'
                ),
            'special_to_date'  => array(
                'type'=>'date', 
                'width'=>'100px', 
                'name' => 'pu_special_to_date[]',
                'renderer' => 'Iksanika_Productupdater_Block_Widget_Grid_Column_Renderer_Datepicker',
                'header'=> Mage::helper('catalog')->__('Spshl TO Date')),
            'special_price'  => array(
                'type'=>'price', 
                'width'=>'30px', 
                'header'=> Mage::helper('catalog')->__('Special Price'), 
                'name' => 'pu_special_price[]',
                'renderer' => 'Iksanika_Productupdater_Block_Widget_Grid_Column_Renderer_Price',
                'currency_code' => $currency),
            'special_from_date'  => array(
                'type' => 'date', 
                'width' => '100px',
                'name' => 'pu_special_from_date[]',
                'renderer' => 'Iksanika_Productupdater_Block_Widget_Grid_Column_Renderer_Datepicker',
                'image'  => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'/adminhtml/default/default/images/grid-cal.gif',
                'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),                
                'header'=> Mage::helper('catalog')->__('Spshl FROM Date')),
            'color'  => array(
                'type'=>'text', 
                'width'=>'70px', 
                'header'=> Mage::helper('catalog')->__('Color')),
            'size'  => array(
                'type'=>'text', 
                'width'=>'70px', 
                'header'=> Mage::helper('catalog')->__('Size')),
            'brand'  => array(
                'type'=>'text', 
                'width'=>'70px', 
                'header'=> Mage::helper('catalog')->__('Brand')),
            'custom_design'  => array(
                'type'=>'text', 
                'width'=>'70px', 
                'header'=> Mage::helper('catalog')->__('Custom Design')),
            'custom_design_from'  => array(
                'type'=>'date', 
                'width'=>'70px', 
                'header'=> Mage::helper('catalog')->__('Custom Design FRM')),
            'custom_design_to'  => array(
                'type'=>'date',
                'width'=>'70px',
                'header'=> Mage::helper('catalog')->__('Custom Design TO')),
            'custom_layout_update'  => array(
                'type'=>'text', 
                'width'=>'90px', 
                'header'=> Mage::helper('catalog')->__('Custom Layout Update'),
                'string_limit' => Mage::getStoreConfig('productupdater/general/truncatelongtextafter')),
            'default_category_id'  => array(
                'type'=>'text',
                'width'=>'70px',
                'header'=> Mage::helper('catalog')->__('Default Categry ID')),
            'dimension'  => array(
                'type'=>'text',
                'width'=>'75px',
                'header'=> Mage::helper('catalog')->__('Dimensions')),
            'manufacturer'  => array(
                'type'=>'text', 
                'width'=>'75px', 
                'header'=> Mage::helper('catalog')->__('Manufacturer')),
            'meta_keyword'  => array(
                'type'=>'iks_textarea', 
/*                'width'=>'200px', */
                'name' => 'pu_meta_keyword[]',
                'index' => 'meta_keyword',
                'header'=> Mage::helper('catalog')->__('Meta Keywds'),
                'renderer' => 'Iksanika_Productupdater_Block_Widget_Grid_Column_Renderer_Textarea'),
            'meta_description'  => array(
                'type'=>'iks_textarea', 
                'name' => 'pu_meta_description[]',
                'index' => 'meta_description',
                'header'=> Mage::helper('catalog')->__('Meta Descr'),
                'renderer' => 'Iksanika_Productupdater_Block_Widget_Grid_Column_Renderer_Textarea'),
            'meta_title'  => array(
                'type'=>'input', 
                'width'=>'250px', 
                'index' => 'meta_title',
                'name' => 'pu_meta_title[]',
                'header'=> Mage::helper('catalog')->__('Meta Title')),
            'short_description'  => array(
                'name' => 'pu_short_description[]',
                'type' => 'iks_textarea',
                'index' => 'short_description',
                'header'=> Mage::helper('catalog')->__('Short Description'), 
                'renderer' => 'Iksanika_Productupdater_Block_Widget_Grid_Column_Renderer_Textarea'),
            'description'  => array(
                'type'=>'iks_textarea', 
                'name' => 'pu_description[]',
                'index' => 'description',
                'renderer' => 'Iksanika_Productupdater_Block_Widget_Grid_Column_Renderer_Textarea',
                'header'=> Mage::helper('catalog')->__('Description')),
            'enable_googlecheckout'  => array(
//                'type'=>'iks_options', 
                'type'=>'options', 
                'name' => 'enable_googlecheckout',
                'index' => 'enable_googlecheckout',
                'options' => array(0 => __('No'), 1 => __('Yes')),
                'renderer' => 'Iksanika_Productupdater_Block_Widget_Grid_Column_Renderer_Options',
                'header'=> Mage::helper('catalog')->__('Enable G-Checkout')),
            'gift_message_available'  => array(
//                'type'=>'iks_options', 
                'type'=>'options', 
                'name' => 'gift_message_available',
                'index' => 'gift_message_available',
                'options' => array(0 => __('No'), 1 => __('Yes')),
                'renderer' => 'Iksanika_Productupdater_Block_Widget_Grid_Column_Renderer_Options',
                'header'=> Mage::helper('catalog')->__('Gift Mess. Allow')),
            'gallery'  => array(
                'type'=>'text', 
                'width'=>'90px', 
                'header'=> Mage::helper('catalog')->__('Gallery')),
            'media_gallery'  => array(
                'type'=>'text', 
                'width'=>'90px', 
                'header'=> Mage::helper('catalog')->__('Media Gallery')),
            'group_price'  => array(
                'type'=>'text', 
                'width'=>'90px', 
                'header'=> Mage::helper('catalog')->__('Group Price')),
            'is_recurring'  => array(
//                'type'=>'iks_options', 
                'type'=>'options', 
                'name' => 'is_recurring',
                'index' => 'is_recurring',
                'options' => array(0 => __('No'), 1 => __('Yes')),
                'renderer' => 'Iksanika_Productupdater_Block_Widget_Grid_Column_Renderer_Options',
                'header'=> Mage::helper('catalog')->__('Allow Recurring')),
            'msrp' => array(
                'header'=> Mage::helper('catalog')->__('MSRP'),
                'type'  => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index' => 'price',
                'name' => 'pu_price[]',
                'renderer' => 'Iksanika_Productupdater_Block_Widget_Grid_Column_Renderer_Price'),
            'msrp_display_actual_price_type'  => array(
                'type'=>'text', 
                'width'=>'90px', 
                'header'=> Mage::helper('catalog')->__('MSRP Display Actual')),
            'msrp_enabled'  => array(
                'type'=>'text', 
                'width'=>'90px', 
                'header'=> Mage::helper('catalog')->__('MSRP Allow')),
            
            'news_from_date'  => array(
                'type'=>'date', 
                'width'=>'100px', 
                'name' => 'pu_news_from_date[]',
                'index' => 'news_from_date',
                'renderer' => 'Iksanika_Productupdater_Block_Widget_Grid_Column_Renderer_Datepicker',
                'header'=> Mage::helper('catalog')->__('New From Date')),
            'news_to_date'  => array(
                'type'=>'date', 
                'width'=>'100px', 
                'name' => 'pu_news_to_date[]',
                'index' => 'news_to_date',
                'renderer' => 'Iksanika_Productupdater_Block_Widget_Grid_Column_Renderer_Datepicker',
                'header'=> Mage::helper('catalog')->__('New To Date')),
            'options_container'  => array(
                'type'=>'text', 
                'width'=>'90px', 
                'header'=> Mage::helper('catalog')->__('Options Cont.')),
            'page_layout'  => array(
                'type'=>'text', 
                'width'=>'90px', 
                'header'=> Mage::helper('catalog')->__('Page Layout')),
            'price_view'  => array(
                'type'=>'text', 
                'width'=>'90px', 
                'header'=> Mage::helper('catalog')->__('Price View')),
            'recurring_profile'  => array(
                'type'=>'text', 
                'width'=>'90px', 
                'header'=> Mage::helper('catalog')->__('Recurring Profile')),
            'url_key'  => array(
                'type'=>'input',
                'index' => 'url_key',
                'width'=>'180px', 
                'header'=> Mage::helper('catalog')->__('Url Key')),

            'related_ids'  => array(
                'type'=>'input',
                'index' => 'related_ids',
                'width'=>'80px',
                'filter_condition_callback' => array($this, '_applyMyFilter'),
                'header'=> Mage::helper('catalog')->__('Related IDs')),
            'cross_sell_ids'  => array(
                'type'=>'input',
                'index' => 'cross_sell_ids',
                'width'=>'80px',
                'filter_condition_callback' => array($this, '_applyMyFilter'),
                'header'=> Mage::helper('catalog')->__('Cross-Sell IDs')),
            'up_sell_ids'  => array(
                'type'=>'input',
                'index' => 'up_sell_ids',
                'width'=>'80px',
                'filter_condition_callback' => array($this, '_applyMyFilter'),
                'header'=> Mage::helper('catalog')->__('Up-Sell IDs')),
            
        );
/*
        foreach(self::$columnSettings as $col => $true) 
        {
            if(isset($ignoreCols[$col])) 
                continue;
            
            if(isset($defaults[$col])) 
            {
                $innerSettings = $defaults[$col];
            } else 
            {
                $innerSettings = array(
                    'header'=> Mage::helper('catalog')->__($col),
                    'width' => '100px',
//                    'type'  => 'text',
                    
                    // enhacement for Franco comcast
                    'type'  => 'input',
                );
            }
            $innerSettings['index'] = $col;
            $this->addColumn($col, $innerSettings);
        }
*/
        
        
        
        
        foreach(self::$columnSettings as $col => $true) 
        {
            if(isset($ignoreCols[$col])) 
                continue;
            
            if(isset($defaults[$col])) 
            {
                $innerSettings = $defaults[$col];
            } else 
            {
                $innerSettings = array(
                    'header'=> Mage::helper('catalog')->__($col),
                    'width' => '100px',
//                    'type'  => 'text',
                    
                    // enhacement for Franco comcast
                    'type'  => 'input',
                );
            }
            $innerSettings['index'] = $col;
            $this->addColumn($col, $innerSettings);
        }

        

        
        $storeId    =  (int) Mage::app()->getRequest()->getParam('store', 0);
        $store  =   Mage::app()->getStore($storeId);
        
        
        $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
                         ->addVisibleFilter()
                         ->addStoreLabel($storeId);
//        $attributes->getSelect()->where($attributes->getConnection()->quoteInto('main_table.attribute_id IN (?)', array('price','qty','cost','is_in_stock', 'name')));
        $list = $attributes->getSelect();
        
        
$attribute = Mage::getModel('eav/entity_attribute')->load('price');
//echo $attribute->getFrontendInput().'test';

echo '<br/><br/>';

//echo get_class($attributes).' '.count($attributes);


        foreach($attributes as $attribute)
        {
//$attribute = Mage::getModel('eav/entity_attribute')->load('price');
//  $attribute->getFrontendInput();

            echo get_class($attribute).' --- '.$attribute->getStoreLabel().' --- '.$attribute->getAttributeCode().' --- '.$attribute->getFrontendInput().'<br/>';
//            var_dump($attribute);
            
        }
        
        foreach($attributes as $attribute)
        {
            $column = array();
        }
        
        
        die();
        
        
        
        // adding cost column
        
        if ($this->_gridAttributes->getSize() > 0)
        {
            Mage::register('ampgrid_grid_attributes', $this->_gridAttributes);
            foreach ($this->_gridAttributes as $attribute)
            {
                $props = array(
                    'header'=> $attribute->getStoreLabel(),
                    'index' => $attribute->getAttributeCode(),
                );
                if ('price' == $attribute->getFrontendInput())
                {
                    $props['type']          = 'price';
                    $props['currency_code'] = $this->_getStore()->getBaseCurrency()->getCode();
                }
                if ('select' == $attribute->getFrontendInput() || 'multiselect' == $attribute->getFrontendInput() || 'boolean' == $attribute->getFrontendInput())
                {
                    $propOptions = array();
                    
                    if ('multiselect' == $attribute->getFrontendInput())
                    {
                        $propOptions['null'] = $this->__('- No value specified -');
                    }
                    
                    if ('custom_design' == $attribute->getAttributeCode())
                    {
                        $allOptions = $attribute->getSource()->getAllOptions();
                        if (is_array($allOptions) && !empty($allOptions))
                        {
                            foreach ($allOptions as $option)
                            {
                                if (!is_array($option['value']))
                                {
                                    if ($option['value'])
                                    {
                                        $propOptions[$option['value']] = $option['value'];
                                    }
                                } else 
                                {
                                    foreach ($option['value'] as $option2)
                                    {
                                        if (isset($option2['value']))
                                        {
                                            $propOptions[$option2['value']] = $option2['value'];
                                        }
                                    }
                                }
                            }
                        }
                    } else 
                    {
                        // getting attribute values with translation
                        $valuesCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                            ->setAttributeFilter($attribute->getId())
                            ->setStoreFilter($this->_getStore()->getId(), false)
                            ->load();
                        if ($valuesCollection->getSize() > 0)
                        {
                            foreach ($valuesCollection as $item) {
                                $propOptions[$item->getId()] = $item->getValue();
                            }
                        } else 
                        {
                            $selectOptions = $attribute->getFrontend()->getSelectOptions();
                            if ($selectOptions)
                            {
                                foreach ($selectOptions as $selectOption)
                                {
                                    $propOptions[$selectOption['value']] = $selectOption['label'];
                                }
                            }
                        }
                    }
                    
                    if ('multiselect' == $attribute->getFrontendInput())
                    {
//                        $props['renderer'] = 'ampgrid/adminhtml_catalog_product_grid_renderer_multiselect';
                        $props['renderer'] = Iksanika_Productupdater_Block_Widget_Grid_Column_Renderer_Multiselect;
//                        $props['filter']   = 'ampgrid/adminhtml_catalog_product_grid_filter_multiselect';
                        $props['filter']   = Iksanika_Productupdater_Block_Widget_Grid_Column_Filter_Multiselect;
                    }
                    
                    $props['type'] = 'options';
                    $props['options'] = $propOptions;
                }
                
                $this->addColumn($attribute->getAttributeCode(), $props);
            }
        }
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        $this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'    => 'getId',
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('catalog')->__('Edit'),
                        'id' => "editlink",
                        'url'     => array(
                            'base'=> 'adminhtml/*/edit',
                            'params'=> array('store'=>$this->getRequest()->getParam('store'))
                        ),
                        'field'   => 'id'
                    )
                ),
        ));

//        $this->addRssList('rss/catalog/notifystock', Mage::helper('catalog')->__('Notify Low Stock RSS'));
        if (Mage::helper('catalog')->isModuleEnabled('Mage_Rss')) 
        {
            $this->addRssList('rss/catalog/notifystock', Mage::helper('catalog')->__('Notify Low Stock RSS'));
        }
        
        $this->addExportType('*/*/exportCsv', Mage::helper('customer')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('customer')->__('XML'));

        $this->setDestElementId('edit_form');
        
        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('product');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'=> Mage::helper('catalog')->__('Delete'),
             'url'  => $this->getUrl('*/*/massDelete'),
             'confirm' => Mage::helper('catalog')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('catalog/product_status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('catalog')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current' => true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('catalog')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));

        $this->getMassactionBlock()->addItem('attributes', 
            array(
                'label' => Mage::helper('catalog')->__('Update attributes'),
                'url'   => $this->getUrl('adminhtml/catalog_product_action_attribute/edit', array('_current'=>true))
                )
        );

        $this->getMassactionBlock()->addItem('otherDivider', $this->getSubDivider("------Additional------"));
        
        $this->getMassactionBlock()->addItem('refreshProducts', 
            array(
                'label' => $this->__('Refresh Products'),
                'url'   => $this->getUrl('*/*/massRefreshProducts', array('_current'=>true))
            )
        );
        
        /*
         * Prepare list of column for update
         */
        $fields = self::getColumnForUpdate();
        
        $this->getMassactionBlock()->addItem('save', 
            array(
                'label' => Mage::helper('catalog')->__('Update'),
                'url'   => $this->getUrl('*/*/massUpdateProducts', array('_current'=>true)),
                'fields' => $fields
            )
        );
        
        $this->getMassactionBlock()->addItem('categoryActionDivider', $this->getCleanDivider());
        $this->getMassactionBlock()->addItem('otherCategoryActionDivider', $this->getDivider("Category"));

        $this->getMassactionBlock()->addItem('addCategory', 
            array(
                'label'     =>  Mage::helper('catalog')->__('Category: Add'),
                'url'       =>  $this->getUrl('*/*/addCategory', array('_current'=>true)),
                'additional'=>  $this->getCategoriesTree($this->__('Category IDs ')),
            )
        );

        $this->getMassactionBlock()->addItem('removeCategory', 
            array(
                'label'     =>  Mage::helper('catalog')->__('Category: Remove'),
                'url'       =>  $this->getUrl('*/*/removeCategory', array('_current'=>true)),
                'additional'=>  $this->getCategoriesTree($this->__('Category IDs ')),
            )
        );
        
        
        if($this->isAllowed['related'] || $this->isAllowed['cross_sell'] || $this->isAllowed['up_sell'])
        {
            $this->getMassactionBlock()->addItem('relatedDivider', $this->getCleanDivider());

            $this->getMassactionBlock()->addItem('otherDividerSalesMotivation', $this->getDivider("Product Relator"));
        }
        
        if($this->isAllowed['related']) 
        {
            $this->getMassactionBlock()->addItem('relatedEachOther', array(
                'label' => $this->__('Related: To Each Other'),
                'url'   => $this->getUrl('*/*/massRelatedEachOther', array('_current'=>true)),
                'callback' => 'specifyRelatedEachOther()',
            ));
            $this->getMassactionBlock()->addItem('relatedTo', array(
                'label' => $this->__('Related: Add ..'),
                'url'   => $this->getUrl('*/*/massRelatedTo', array('_current'=>true)),
                'callback' => 'specifyRelatedProducts()'
            ));
            $this->getMassactionBlock()->addItem('relatedClean', array(
                'label' => $this->__('Related: Clear'),
                'url'   => $this->getUrl('*/*/massRelatedClean', array('_current'=>true)),
                'callback' => 'specifyRelatedClean()'
            ));
        }
        
        
        if($this->isAllowed['cross_sell']) 
        {
            $this->getMassactionBlock()->addItem('crossSellDivider', $this->getCleanDivider());

            $this->getMassactionBlock()->addItem('crossSellEachOther', array(
                'label' => $this->__('Cross-Sell: To Each Other'),
                'url'   => $this->getUrl('*/*/massCrossSellEachOther', array('_current'=>true)),
                'callback' => 'specifyCrossSellEachOther()',
            ));
            $this->getMassactionBlock()->addItem('crossSellTo', array(
                'label' => $this->__('Cross-Sell: Add ..'),
                'url'   => $this->getUrl('*/*/massCrossSellTo', array('_current'=>true)),
                 'callback' => 'chooseWhatToCrossSellTo()'
            ));
            $this->getMassactionBlock()->addItem('crossSellClear', array(
                'label' => $this->__('Cross-Sell: Clear'),
                'url'   => $this->getUrl('*/*/massCrossSellClear', array('_current'=>true)),
                'callback' => 'specifyCrossSellClean()',
            ));
        }
        
        
        if($this->isAllowed['up_sell']) 
        {
            $this->getMassactionBlock()->addItem('upSellDivider', $this->getCleanDivider());
            
            $this->getMassactionBlock()->addItem('upSellTo', array(
                'label' => $this->__('Up-Sells: Add ..'),
                'url'   => $this->getUrl('*/*/massUpSellTo', array('_current'=>true)),
                 'callback' => 'chooseWhatToUpSellTo()'
            ));
            $this->getMassactionBlock()->addItem('upSellClear', array(
                'label' => $this->__('Up-Sells: Clear'),
                'url'   => $this->getUrl('*/*/massUpSellClear', array('_current'=>true)),
                'callback' => 'specifyUpSellClean()',
            ));
        }
        
        return $this;
    }
    
    
    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/catalog_product/edit', array(
            'store'=>$this->getRequest()->getParam('store'),
            'id'=>$row->getId())
        );
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
    
    protected function getDivider($divider="*******") {
        $dividerTemplate = array(
          'label' => '********'.$this->__($divider).'********',
          'url'   => $this->getUrl('*/*/index', array('_current'=>true)),
          'callback' => "null"
        );
        return $dividerTemplate;
    }

    protected function getSubDivider($divider="-------") {
        $dividerTemplate = array(
          'label' => '--------'.$this->__($divider).'--------',
          'url'   => $this->getUrl('*/*/index', array('_current'=>true)),
          'callback' => "null"
        );
        return $dividerTemplate;
    }

    protected function getCleanDivider() {
        $dividerTemplate = array(
          'label' => ' ',
          'url'   => $this->getUrl('*/*/index', array('_current'=>true)),
          'callback' => "null"
        );
        return $dividerTemplate;
    }
    
    public function getCsv()
    {
        $csv = '';
        $this->_isExport = true;
        $this->_prepareGrid();
        $this->getCollection()->getSelect()->limit();
        $this->getCollection()->setPageSize(0);
        $this->getCollection()->load();
        $this->_afterLoadCollection();
        $data = array();
        foreach ($this->_columns as $column) {
            if (!$column->getIsSystem()) {
                $data[] = '"'.$column->getExportHeader().'"';
            }
        }
        $csv.= implode(',', $data)."\n";

        
        foreach ($this->getCollection() as $item) {
            $data = array();
            foreach ($this->_columns as $column) {
                if (!$column->getIsSystem()) 
                {
                    $colIndex = $column->getIndex();
                    $colContent = $item->$colIndex;
                    if($colIndex == 'category_ids')
                        $colContent = implode(',', $item->getCategoryIds());
                    $data[] = '"'.str_replace(array('"', '\\'), array('""', '\\\\'), $colContent).'"';
                }
            }
            $csv.= implode(',', $data)."\n";
        }

        if ($this->getCountTotals())
        {
            $data = array();
            foreach ($this->_columns as $column) {
                if (!$column->getIsSystem()) {
                    $data[] = '"' . str_replace(array('"', '\\'), array('""', '\\\\'),
                        $column->getRowFieldExport($this->getTotals())) . '"';
                }
            }
            $csv.= implode(',', $data)."\n";
        }

        return $csv;
    }

    
    
    
    
    
    
    protected function getCategoriesTree($title)
    {
        $element = array('category_value' => array(
            'name'  =>  'category',
            'type'  =>  'text',
            'class' =>  'required-entry',
            'label' =>  $title,
        ));
        
        if (Mage::getStoreConfig('productupdater/massactions/categorynames', Mage::helper('productupdater')->getStoreId()))
        { 
            $rootId = Mage::app()->getStore(Mage::helper('productupdater')->getStoreId())->getRootCategoryId();
            $element['category_value']['label']   =   Mage::helper('productupdater')->__('Category');
            $element['category_value']['type']    =   'select';
            $element['category_value']['values']  =   Mage::helper('productupdater/category')->getTree($rootId);
        } 
        
        return $element;      
    } 
    
    
}