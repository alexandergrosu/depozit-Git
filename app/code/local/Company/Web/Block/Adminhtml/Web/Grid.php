<?php

class Company_Web_Block_Adminhtml_Web_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
 public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
		
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'sales/order_grid_collection';
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
		 $collection -> addFieldToFilter('state', 'complete');
            $this->setCollection($collection);
        return parent::_prepareCollection();
		
    }

    protected function _prepareColumns()
    {

        $this->addColumn('real_order_id', array(
            'header'=> Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'type'  => 'text',
            'filter' => false,
            'index' => 'increment_id',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('sales')->__('Purchased From (Store)'),
                'index'     => 'store_id',
                'type'      => 'store',
                'store_view'=> true,
                'display_deleted' => true,
            ));
        }

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'filter' => false,
            'width' => '100px',
        ));

        $this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Bill to Name'),
            'index' => 'billing_name',
            'filter' => false,
        ));
		$this->addColumn('shipping_company', array(
				'header' => Mage::helper('sales')->__('Company'),
				'index' => 'shipping_company',
				'width' => '160px',
				'filter' => false,
				),'billing_name');
				
		$this->addColumn('skus', array(
				'header' => Mage::helper('sales')->__('SKU'),
				'index' => 'skus',
				'filter' => false,
				),'billing_name');
				
		$this->addColumn('comment', array(
				'header' => Mage::helper('sales')->__('Comentarii comanda'),
				'index' => 'comment',
				'filter' => false,
				),'billing_name');

        $this->addColumn('base_grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Base)'),
            'index' => 'base_grand_total',
            'type'  => 'currency',
            'filter' => false,
            'currency' => 'base_currency_code',
        ));

        $this->addColumn('grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
            'index' => 'grand_total',
            'filter' => false,
            'type'  => 'currency',
            'currency' => 'order_currency_code',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type'  => 'options',
            'width' => '70px',
            'filter' => false,
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ));
		$this->addColumn('state', array(
				'header' => Mage::helper('sales')->__('state'),
				'index' => 'state',
				'width' => '50',
				'filter' => false,
				'type'  => 'options',
                'options' => Mage::getSingleton('sales/order_config')->getStates(),
				));

   
        
        return parent::_prepareColumns();
    }

   
    public function getRowUrl($row)
    {
        
             
    if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
        return $this->getUrl('adminhtml/sales_order/view', array('order_id' => $row->getId()));
    }
    return false;

       
    }
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

}