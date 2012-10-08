<?php
 # Controllers are not autoloaded so we will have to do it manually:
   	require_once 'Mage/Adminhtml/Controller/Action.php';
    class Briel_Inlineedit_Catalog_ProductController extends Mage_Adminhtml_Controller_Action
    {
    public function indexAction()
	    {   
	    	$this->loadLayout();	
	    	$block = $this->getLayout()->createBlock(
			    'Mage_Core_Block_Template',
			    'my_block_name_here',
				array('template' => 'briel_inlineedit/inline-edit.phtml'));
	    	$this->getLayout()->getBlock('js')->append($block);
	    	$this->renderLayout();
	    }
		
	/*public function updateFutureStockQtyAction()
		{
    		
			$fieldId = (int) $this->getRequest()->getParam('id');
			$_product = Mage::getModel('catalog/product')->load($fieldId);
		    $sku = $_product->getData('sku');//load product sku
		    $skuexplodat = explode(".",$sku);
		    $skufinal = $skuexplodat[0];
			$title = $this->getRequest()->getParam('title');
			$products = Mage::getResourceModel('catalog/product_collection')->addAttributeToSelect('*')->addAttributeToFilter('sku', array('like'=>"$skufinal.%"));
    		foreach($products as $val){
		        $skuforeachproduct = $val->getData('sku');
    			$idbysku = Mage::getModel('catalog/product')->getIdBySku($skuforeachproduct);
				$idbyskuarray = array('' => $idbysku);
					foreach($idbyskuarray as $mod){
						$stoc = Mage::getModel('catalog/product')->load($mod);
						$stoc -> setData('future_stock_qty',$title);
						$stoc -> save();
										  		  }
									  }
		}*/
		
	/*public function updateFutureStockDateAction()
		{
    		
			$fieldId = (int) $this->getRequest()->getParam('id');
			$_product = Mage::getModel('catalog/product')->load($fieldId);
		    $sku = $_product->getData('sku');//load product sku
		    $skuexplodat = explode(".",$sku);
		    $skufinal = $skuexplodat[0];
			
			$title = $this->getRequest()->getParam('title');
			$products = Mage::getResourceModel('catalog/product_collection')->addAttributeToSelect('*')->addAttributeToFilter('sku', array('like'=>"$skufinal.%"));
			
    		foreach($products as $val){
		        $skuforeachproduct = $val->getData('sku');
    			$idbysku = Mage::getModel('catalog/product')->getIdBySku($skuforeachproduct);
				$idbyskuarray = array('' => $idbysku);
					foreach($idbyskuarray as $mod){
						$stoc = Mage::getModel('catalog/product')->load($mod);
						$stoc -> setData('future_stock_date',$title);
						$stoc -> save();
										  		  }
									  }
		}*/
		public function updateFutureStockDateAction()
		{
    		
			$fieldId = (int) $this->getRequest()->getParam('id');
			$_product = Mage::getModel('catalog/product')->load($fieldId);
		    $sku = $_product->getData('sku');//load product sku
		    $skuexplodat = explode(".",$sku);
		    $skufinal = $skuexplodat[0];
			$title = $this->getRequest()->getParam('title');
			$products = Mage::getResourceModel('catalog/product_collection')->addAttributeToSelect('*')->addAttributeToFilter('sku', array('like'=>"$skufinal.%"));
    		foreach($products as $val){
		       $val -> setFutureStockDate($title);
			   $val -> save();
									  }
		}
		public function updateFutureStockQtyAction()
		{
    		
			$fieldId = (int) $this->getRequest()->getParam('id');
			$_product = Mage::getModel('catalog/product')->load($fieldId);
		    $sku = $_product->getData('sku');//load product sku
		    $skuexplodat = explode(".",$sku);
		    $skufinal = $skuexplodat[0];
			$title = $this->getRequest()->getParam('title');
			$products = Mage::getResourceModel('catalog/product_collection')->addAttributeToSelect('*')->addAttributeToFilter('sku', array('like'=>"$skufinal.%"));
    		foreach($products as $val){
		        $val -> setFutureStockQty($title);
				$val -> save();
									  }
		}
    	
	public function updateTitleAction()
		{
		   	$fieldId = $this->getRequest() -> getParam('id');//load product id
			$_product = Mage::getModel('catalog/product')->load($fieldId);
			$sku = $_product->getData('sku');//load product sku
			$skuexplodat = explode(".",$sku);
			$skufinal = $skuexplodat[0];
			$qty = $this -> getRequest() -> getParam('title');//input value
			$products = Mage::getResourceModel('catalog/product_collection')->addAttributeToSelect('*')->addAttributeToFilter('sku', array('like'=>"$skufinal.%"));
			if ($products){
				
			foreach ($products as $val) {
	    		$stoc = Mage::getModel('cataloginventory/stock_item') -> loadByProduct($val);
				$stoc -> setQty($qty);
				$stoc -> save();}
					Mage::getSingleton('adminhtml/session') -> addSuccess(
		        	Mage::helper('adminhtml') -> __(
		        	'Cantitatea pentru produsele cu SKU: "'.$skufinal.'" a fost modificata cu succes'));
						 }
	   }
	}