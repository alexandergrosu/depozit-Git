<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales orders grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
		//--------------------------------filtru comenzi-----------------------------------
		//$collection->addFieldToFilter('state', array(  
    	//array('state'=>'state','processing'),             
    	//array('state'=>'state','new') ));
		//--------------------------------filtru comenzi-----------------------------------
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

	
    protected function _prepareColumns()
    {

        $this->addColumn('real_order_id', array(
            'header'=> Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'type'  => 'text',
            'filter_index' =>'main_table.increment_id',
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
            'filter_index' => 'main_table.created_at',
            'width' => '100px',
        ));

        $this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Bill to Name'),
            'width' => '160px',
            'index' => 'billing_name',
			
        ));
       /* $this->addColumn('shipping_name', array(
            'header' => Mage::helper('sales')->__('Ship to Name'),
            'index' => 'shipping_name',
        ));*/
		
        $this->addColumn('base_grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Base)'),
            'index' => 'base_grand_total',
            'type'  => 'currency',
            'filter_index' => 'main_table.base_grand_total',
            'currency' => 'base_currency_code',
        ));

        /*$this->addColumn('grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
            'index' => 'grand_total',
            'filter_index' => 'main_table.grand_total',
            'type'  => 'currency',
            'currency' => 'order_currency_code',
        ));*/

        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type'  => 'options',
            'filter_index' => 'main_table.status',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ));

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            $this->addColumn('action',
                array(
                    'header'    => Mage::helper('sales')->__('Action'),
                    'width'     => '40px',
                    'type'      => 'action',
                    'getter'     => 'getId',
                    'actions'   => array(
                        array(
                            'caption' => Mage::helper('sales')->__('View'),
                            'url'     => array('base'=>'*/sales_order/view'),
                            'field'   => 'order_id'
                        )
                    ),
                    'filter'    => false,
                    'sortable'  => false,
                    'index'     => 'stores',
                    'is_system' => true,
            ));
        }
        $this->addRssList('rss/order/new', Mage::helper('sales')->__('New Order RSS'));

        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('order_ids');
        $this->getMassactionBlock()->setUseSelectAll(false);
 		parent::_prepareMassaction();
		
		 $this->getMassactionBlock()->addItem('Anuleaza', array(
	                'label' => Mage::helper('sales')->__('Anuleaza'),
			'url' 	  => $this->getUrl('*/sales_order/canceled'),
		 ));
		
     //	if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/cancel')) {
     // 	$this->getMassactionBlock()->addItem('cancel_order', array(
     //    		'label'=> Mage::helper('sales')->__('Cancel'),
     //      	'url'  => $this->getUrl('*/sales_order/massCancel'),
     // 	));
     //   }

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/hold')) {
            $this->getMassactionBlock()->addItem('hold_order', array(
                 'label'=> Mage::helper('sales')->__('Hold'),
                 'url'  => $this->getUrl('*/sales_order/massHold'),
            ));
        }

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/unhold')) {
            $this->getMassactionBlock()->addItem('unhold_order', array(
                 'label'=> Mage::helper('sales')->__('Unhold'),
                 'url'  => $this->getUrl('*/sales_order/massUnhold'),
            ));
        }

        $this->getMassactionBlock()->addItem('pdfinvoices_order', array(
             'label'=> Mage::helper('sales')->__('Print Invoices'),
             'url'  => $this->getUrl('*/sales_order/pdfinvoices'),
        ));

        $this->getMassactionBlock()->addItem('pdfshipments_order', array(
             'label'=> Mage::helper('sales')->__('Print Packingslips'),
             'url'  => $this->getUrl('*/sales_order/pdfshipments'),
        ));

        $this->getMassactionBlock()->addItem('pdfcreditmemos_order', array(
             'label'=> Mage::helper('sales')->__('Print Credit Memos'),
             'url'  => $this->getUrl('*/sales_order/pdfcreditmemos'),
        ));

        $this->getMassactionBlock()->addItem('pdfdocs_order', array(
             'label'=> Mage::helper('sales')->__('Print All'),
             'url'  => $this->getUrl('*/sales_order/pdfdocs'),
        ));

        $this->getMassactionBlock()->addItem('print_shipping_label', array(
             'label'=> Mage::helper('sales')->__('Print Shipping Labels'),
             'url'  => $this->getUrl('*/sales_order_shipment/massPrintShippingLabel'),
        ));
		
       
 //--------------------------NEW ACTIONS BY GROSU ALEX------------------------------------------
 
		$this->getMassactionBlock()->addItem('------------------', array(
	                'label' => Mage::helper('sales')->__('--------------------factura----------------------'),
	    ));
		$this->getMassactionBlock()->addItem('Facturare ', array(
	               'label' => Mage::helper('sales')->__('Facturare'),
					'url' 	  => $this->getUrl('*/sales_order/facturare'),
	    ));
		$this->getMassactionBlock()->addItem('Capture', array(
	                'label' => Mage::helper('sales')->__('Comanda Platita'),
			'url' 	  => $this->getUrl('*/sales_order/massCapture'),
		 ));
		 
		$this->getMassactionBlock()->addItem('--------------------', array(
	                'label' => Mage::helper('sales')->__('---------------schimba status---------------'),
	    ));
		$this->getMassactionBlock()->addItem('in asteptare plata', array(
	                'label' => Mage::helper('sales')->__('In asteptare plata'),
			'url' 	  => $this->getUrl('*/sales_order/inasteptareplata'),
		 ));
		 $this->getMassactionBlock()->addItem('in asteptare stoc', array(
	                'label' => Mage::helper('sales')->__('In asteptare stoc'),
			'url' 	  => $this->getUrl('*/sales_order/inasteptarestoc'),
		 ));
		 $this->getMassactionBlock()->addItem('Livrare Amanata', array(
	                'label' => Mage::helper('sales')->__('Livrare amanata'),
			'url' 	  => $this->getUrl('*/sales_order/livrareamanata'),
		 ));
		 $this->getMassactionBlock()->addItem('Nu raspunde', array(
	                'label' => Mage::helper('sales')->__('Nu raspunde'),
			'url' 	  => $this->getUrl('*/sales_order/nuraspunde'),
		 ));
		  $this->getMassactionBlock()->addItem('Necesita detalii', array(
	                'label' => Mage::helper('sales')->__('Necesita detalii'),
			'url' 	  => $this->getUrl('*/sales_order/necesitadetalii'),
		 ));
		 $this->getMassactionBlock()->addItem('Contactat prin e-mail', array(
	                'label' => Mage::helper('sales')->__('Contactat prin e-mail'),
			'url' 	  => $this->getUrl('*/sales_order/contactatprinemail'),
		 ));
		 $this->getMassactionBlock()->addItem('De livrat cu Urgent', array(
	                'label' => Mage::helper('sales')->__('De livrat cu Urgent'),
			'url' 	  => $this->getUrl('*/sales_order/delivratcuurgent'),
		 ));
		  $this->getMassactionBlock()->addItem('De livrat cu TNT', array(
	                'label' => Mage::helper('sales')->__('De livrat cu TNT'),
			'url' 	  => $this->getUrl('*/sales_order/delivratcutnt'),
		 ));
		 $this->getMassactionBlock()->addItem('De livrat', array(
	                'label' => Mage::helper('sales')->__('De livrat'),
			'url' 	  => $this->getUrl('*/sales_order/processing'),
		 ));
		 $this->getMassactionBlock()->addItem('Livrat cu Urgent', array(
	                'label' => Mage::helper('sales')->__('Livrat cu Urgent'),
			'url' 	  => $this->getUrl('*/sales_order/livratcuurgent'),
		 ));
		  $this->getMassactionBlock()->addItem('Livrat cu TNT', array(
	                'label' => Mage::helper('sales')->__('Livrat cu TNT'),
			'url' 	  => $this->getUrl('*/sales_order/livratcutnt'),
		 ));
		 $this->getMassactionBlock()->addItem('Livrat', array(
	                'label' => Mage::helper('sales')->__('Livrat'),
			'url' 	  => $this->getUrl('*/sales_order/livrat'),
		 ));
		  $this->getMassactionBlock()->addItem('Finalizata', array(
	                'label' => Mage::helper('sales')->__('Finalizata'),
			'url' 	  => $this->getUrl('*/sales_order/complete'),
		 ));
		  
		 
 //--------------------------NEW ACTIONS BY GROSU ALEX------------------------------------------

        return $this;
    }

    public function getRowUrl($row)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            return $this->getUrl('*/sales_order/view', array('order_id' => $row->getId()));
        }
        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

}
