<?php
class Excellence_Salesgrid_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{


	protected function _addColumnFilterToCollection($column)
	{
		if ($this->getCollection()) {
			if ($column->getId() == 'shipping_company') {
				$cond = $column->getFilter()->getCondition();
				$field = 't4.company';
				$this->getCollection()->addFieldToFilter($field , $cond);
				return $this;
			}else if($column->getId() == 'comment'){
				$cond = $column->getFilter()->getCondition();
				$field = 't2.comment';
				$this->getCollection()->addFieldToFilter($field , $cond);
				return $this;
			}else if($column->getId() == 'created'){
				$cond = $column->getFilter()->getCondition();
				$field = 't1.created_at';
				$this->getCollection()->addFieldToFilter($field , $cond);
				return $this;
			/*}else if($column->getId() == 'skus'){
				$cond = $column->getFilter()->getCondition();
				$field = 't6.sku';
				$this->getCollection()->joinSkus();
				$this->getCollection()->addFieldToFilter($field , $cond);
				return $this;*/
			}else if($column->getId() == 'sku'){
				$cond = $column->getFilter()->getCondition();
				$field = 't3.sku';
				$this->getCollection()->addFieldToFilter($field , $cond);
				return $this;
			}else if($column->getId() == 'state'){
				$cond = $column->getFilter()->getCondition();
				$field = 't10.state';
				$this->getCollection()->addFieldToFilter($field , $cond);
				return $this;
			}else{
				return parent::_addColumnFilterToCollection($column);
			}
		}
	}

	protected function _prepareColumns()
	{
		$this->addColumnAfter('state', array(
				'header' => Mage::helper('sales')->__('state'),
				'index' => 'state',
				'width' => '50',
				'type'  => 'options',
                'options' => Mage::getSingleton('sales/order_config')->getStates(),
				),'status');
		$this->addColumnAfter('created', array(
				'header' => Mage::helper('sales')->__('Ultima modificare'),
				'index' => 'created',
				//'type'=>'datetime',
				'width' => '140',
				),'billing_name');
		$this->addColumnAfter('comment', array(
				'header' => Mage::helper('sales')->__('Comentarii comanda'),
				'index' => 'comment',
				),'billing_name');
		/*$this->addColumnAfter('skus', array(
				'header' => Mage::helper('sales')->__('SKU'),
				'index' => 'skus',
				),'billing_name');*/
		$this->addColumnAfter('sku', array(
				'header' => Mage::helper('sales')->__('SKU'),
				'index' => 'sku',
				),'billing_name');
		$this->addColumnAfter('shipping_company', array(
				'header' => Mage::helper('sales')->__('Company'),
				'index' => 'shipping_company',
				'width' => '160px',
				),'billing_name');
				
		return parent::_prepareColumns();
	}
	

}