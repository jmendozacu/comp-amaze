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

class Apptha_Amazereviews_Block_Adminhtml_Amazereviews_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
  	
      parent::__construct();
      
      //SET'S ID FOR OUR GRID
      $this->setId('amazereviewsGrid');
      //SORTING COLUMN TO USE IN OUR GRID
      $this->setDefaultSort('review_id');
      //SET DEFAULT SORTING ORDER
      $this->setDefaultDir('ASC');
      //SET'S GRID OPERATION IN SESSION
      $this->setSaveParametersInSession(true);
  }

  //RETURNS THE COLLECTION OF MODULE WE WANT TO SHOW IN GRID
  protected function _prepareCollection()
  {
  	//COLLECTS DATA FROM THE TABLE amazereviews
      $collection = Mage::getModel('amazereviews/amazereviews')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }
//ADD COLUMNS TO OUR GRID
  protected function _prepareColumns()
  {
  	   //‘abuse_id’ AN UNIQUE ID FOR COLUMN   
  	   //'header' IS NAME OF THE COLUMN 
  	  // ‘index’ IS THE FIELD FROM OUR COLLECTION
  	  //'width' SIZE OF THE COLUMN
  	  //'align' ALIGNMENT WHERE TO DISPLAY THE VALUES
  	  //'filter_index' CREATE FILTER OF THAT COLUMN
  	  //'type' SET TYPE LIKE 'datetime' TO DISPLAY DATE AND 'options' TO DISPLAY DROP DOWN VALUES  

      //ADD COLUMN TO DISPLAY REVIEW ID

      $this->addColumn('review_id', array(
          'header'    => Mage::helper('amazereviews')->__('Review Id'),
          'align'     =>'right',
          'width'     => 1,
          'index'     => 'review_id',
      ));

      //ADD COLUMN TO DISPLAY REPORTED DATE AND TIME
      
      $this->addColumn('created_date', array(
			'header'    => Mage::helper('amazereviews')->__('Reported On'),
			'align'         => 'left',
            'type'          => 'datetime',
            'width'         => 1,
            'filter_index'  => 'created_date',
            'index'         => 'created_date',
      ));

      //ADD COLUMN TO DISPLAY REPORTER COMMENTS

      $this->addColumn('comment', array(
          'header'    => Mage::helper('amazereviews')->__('Reporter Comments'),
          'align'     =>'left',
          'index'     => 'comment',
      ));

      //ADD COLUMN TO DISPLAY REPORTER NAME
      
      $this->addColumn('customer', array(
			'header'    => Mage::helper('amazereviews')->__('Reporter Name'),
			'width'     => 1,
			'index'     => 'customer',
      ));

      //ADD COLUMN TO DISPLAY EMAIL
      
      $this->addColumn('email', array(
			'header'    => Mage::helper('amazereviews')->__('Email'),
			'width'     => '150px',
			'index'     => 'email',
      ));

      //ADD COLUMN TO DISPLAY PRODUCT NAME
          
	  $this->addColumn('product', array(
			'header'    => Mage::helper('amazereviews')->__('Product Name'),
			'width'     => '150px',
			'index'     => 'product',
      ));

      //ADD COLUMN TO DISPLAY STATUS
    
    /*$this->addColumn('status', array(
          'header'    => Mage::helper('amazereviews')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Pending',
              2 => 'Accepted',
              3 => 'Denied',
              
          ),
      ));
*/
      //ADD COLUMN TO DISPLAY ACTION TO VIEW REVIEW
      
	  $this->addColumn('view', array(
            'header'    => Mage::helper('customer')->__('View'),
            'width'     => '10px',
            'sortable'  => false,
           'filter'    => false,
          'renderer'  => 'Apptha_Amazereviews_Block_Adminhtml_Amazereviews_Grid_Renderer_Viewreview',
        ));
      
        //TO EXPORT THE REPORTS AS CSV FILE FORMAT
		$this->addExportType('*/*/exportCsv', Mage::helper('amazereviews')->__('CSV'));
		
		//TO EXPORT THE REPORTS AS XML FILE FORMAT
		$this->addExportType('*/*/exportXml', Mage::helper('amazereviews')->__('XML'));
	  
      return parent::_prepareColumns();
  }
//TO DO OPERATIONS ON MULTIPLE ROWS TOGETHER
    protected function _prepareMassaction()
    {
    	//‘abuse_id’ IS THE DATABASE COLUMN THAT SERVES AS THE UNIQUE IDENTIFIER
        $this->setMassactionIdField('abuse_id');
        //URL PARAMETERS IN WHICH ALL THE 'abuse_id' ARE PASSED TO THE CONTROLLER
		$this->getMassactionBlock()->setFormFieldName('amazereviews');
		
        $this->setMassactionIdFieldOnlyIndexValue(true);
        $this->getMassactionBlock()->setFormFieldName('amazereviews');
        
		// ROUTIN STRING ‘*/*/massDelete’ WILL TRIGER A METHOD CALLED 'massDeleteAction()' IN YOUR CONTROLLER
		//SHOWS THE USER A CONFIRM DIALOG BEFORE SUBMITTING THE URL
        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('amazereviews')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('amazereviews')->__('Are you sure?')
        ));
		//STATUS OPTIONS  
        /*$statuses = Mage::getSingleton('amazereviews/status')->getOptionArray();
        //TO CHANGE STATUS FOR THE SELECTED ROWS
        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('amazereviews')->__('Change status'),
             'url'  => $this->getUrl('massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('amazereviews')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;*/
    }
    // THIS IS THE URL WHICH IS CALLED IN THE AJAX REQUEST, TO GET THE CONTENT OF THE GRID
	public function getRowUrl($row)
 	{  
            return Null;
      
    }

}