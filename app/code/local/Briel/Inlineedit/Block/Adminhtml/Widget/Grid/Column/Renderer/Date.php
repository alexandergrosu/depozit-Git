<?php
	class Briel_Inlineedit_Block_Adminhtml_Widget_Grid_Column_Renderer_Date extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
	{
	    public function render(Varien_Object $row)
	    {
	    	$data = $row->getData($this->getColumn()->getIndex());
			$data = explode(" ", $data);
			$data = $data[0];
			
	    	$html = parent::render($row);
			$html = '<input class="input_renderer_date" type="text" ';
        	$html .= 'name="' . $this->getColumn()->getId() . '" ';
			$html .= 'onFocus="this.select()"';
        	$html .= 'value="' . $data . '"';
        	$html .= 'class="input-text ' . $this->getColumn()->getInlineCss() . '"/>';
	        $html .= '<input class="input_renderer_date_submit" type="submit" onclick="updateFutureStockDate(this, '. $row->getId() .'); return false" value="' . Mage::helper('inlineedit')->__('ok') . '"/>';
	        return $html ; 
	    }
	}
	